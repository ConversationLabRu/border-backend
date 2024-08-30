<?php

namespace App\Http\directions\borderCrossings\reports\Services;

use App\Http\directions\borderCrossings\Dto\CityDTO;
use App\Http\directions\borderCrossings\Dto\CountryDTO;
use App\Http\directions\borderCrossings\Dto\DirectionCrossingDTO;
use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use App\Http\directions\borderCrossings\reports\DTO\AllReportDTO;
use App\Http\directions\borderCrossings\reports\DTO\LastReportDTO;
use App\Http\directions\borderCrossings\reports\Entities\Report;
use App\Http\directions\borderCrossings\reports\Exceptions\TimeExpiredDeletedException;
use App\Http\directions\borderCrossings\reports\transports\DTO\TransportDTO;
use App\Http\directions\borderCrossings\Services\BorderCrossingService;
use App\Utils\LogUtils;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Nette\Schema\ValidationException;

class ReportService
{
    public function getLastReportByBorderCrossing(Request $request)
    {
        $borderCrossingId = (int) $request->query("borderCrossingId");

        if ($borderCrossingId == 0) throw new \ArgumentCountError("Не передан borderCrossingId");


        $reports = Report::where('border_crossing_id', $borderCrossingId)
            ->orderBy('checkpoint_exit', 'desc') // Сортировка по дате в порядке убывания
            ->limit(6) // Ограничение результата до 6 записей
            ->get(); // Получение результата

        LogUtils::elasticLog($request, "Получил последние 6 отчетов погран-перехода: ".$borderCrossingId);

        $result = $reports->map(function (Report $report) {

            $reportDTO = new LastReportDTO(
                $report->getAttributeValue("checkpoint_entry"),
                $report->getAttributeValue("checkpoint_exit"),
                $report->getAttributeValue("checkpoint_queue"),
                $report->getAttributeValue("comment"),
                $report->getAttributeValue("is_flipped_direction"),
                $report->getAttributeValue("user_id"),
                $report->getAttributeValue("time_enter_waiting_area"),
                $this->convertDiffTimeToText($report)
            );

            return $reportDTO->toArray();

        });

        return $result;
    }

    public function getAllReportByBorderCrossing(Request $request)
    {
        $borderCrossingId = (int) $request->query("borderCrossingId");

        if ($borderCrossingId == 0) throw new \ArgumentCountError("Не передан borderCrossingId");

        $reports = Report::with('transport')
            ->where("border_crossing_id", $borderCrossingId)
            ->orderBy('checkpoint_exit', 'desc')
            ->limit(20)
            ->get();

        LogUtils::elasticLog($request, "Перешел на страницу со всеми отчетами по погран-переходу: ".$borderCrossingId);


        $result = $reports->map(function (Report $report) {

            $reportTimestamp = Carbon::parse($report->getAttributeValue("create_report_timestamp"));
            $diffInSeconds = now()->diffInSeconds($reportTimestamp);

            $reportDTO = new AllReportDTO(
                $report->getAttributeValue("checkpoint_entry"),
                $report->getAttributeValue("checkpoint_exit"),
                $report->getAttributeValue("checkpoint_queue"),
                strip_tags($report->getAttributeValue("comment")),
                $report->getAttributeValue("is_flipped_direction"),
                $report->getAttributeValue("id"),
                $report->transport,
                $report->getAttributeValue("user_id"),
                $report->getAttributeValue("time_enter_waiting_area"),
                $this->convertDiffTimeToText($report),
                $diffInSeconds <= 3600
            );

            return $reportDTO->toArray();

        });

        return $result;
    }

    public function deleteReportById(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:reports,id',
        ]);

        $headerString = explode(" ", $request->header('Authorization'))[1];

        // Парсинг строки запроса
        parse_str($headerString, $params);

        // Декодирование параметра user
        $userData = json_decode(urldecode($params['user']), true);

        // Получение user id
        $userId = $userData['id'];

        $id = $validatedData['id'];

        LogUtils::elasticLog($request, "Удаление отчета: ". $id);

        $report = Report::find($id);

        $reportTimestamp = Carbon::parse($report->getAttributeValue("create_report_timestamp"));
        $diffInSeconds = now()->diffInSeconds($reportTimestamp);

        // Второй id - это Константина

        // 3600 - кол-во секунд в одном часе

        if ($report && ( ( ($report->getAttributeValue("user_id") == $userId) && $diffInSeconds <= 3600)
                || ($userId == 241666959 || $userId == 747551551))) {
            $report->delete();
        } else {
            LogUtils::elasticLog($request, "Истекло время удаления отчета ". $id);
            throw new TimeExpiredDeletedException("Истекло время удаления");
        }

    }

    public function createReport(Request $request)
    {
        // Валидация данных
        $request->validate([
            'border_crossing_id' => 'required|exists:borderсrossings,id',
            'transport_id' => 'required|exists:transports,id',
            'user_id' => 'required|integer',
            'checkpoint_queue' => 'nullable|date',
            'checkpoint_entry' => 'required|date',
            'checkpoint_exit' => 'required|date',
            'comment' => 'nullable|string',
            'is_flipped_direction' => 'nullable|boolean',
            'time_enter_waiting_area' => 'nullable|date',
        ]);

        // Создание экземпляра модели
        $report = new Report();

        // Заполнение модели данными из запроса
        $report->fill($request->only([
            'border_crossing_id',
            'transport_id',
            'user_id',
            'checkpoint_queue',
            'checkpoint_entry',
            'checkpoint_exit',
            'comment',
            'is_flipped_direction',
            'time_enter_waiting_area'
        ]));

        $report->create_report_timestamp = now();

        $report->save();

        LogUtils::elasticLog($request, "Создал отчет ");

        return $report;

    }

    public function convertDiffTimeToText(Report $report): string
    {
        $entryTime = new DateTime($report["checkpoint_entry"], new DateTimeZone('Europe/Minsk'));
        $exitTime = new DateTime($report["checkpoint_exit"], new DateTimeZone('Europe/Minsk'));

        $differenceInMs = $exitTime->diff($entryTime);

        if (!is_null($report["time_enter_waiting_area"])) {

            $enterWaitingAreaTime = new DateTime($report["time_enter_waiting_area"], new DateTimeZone('Europe/Minsk'));

            $differenceInMs = $exitTime->diff($enterWaitingAreaTime);
        }

        if (!is_null($report["checkpoint_queue"])) {
            $queueTime = new DateTime($report["checkpoint_queue"], new DateTimeZone('Europe/Minsk'));

            $differenceInMs = $exitTime->diff($queueTime);
        }

        // Получаем разницу в часах и минутах
        $totalHours = $differenceInMs->days * 24 + $differenceInMs->h; // Учитываем дни в часы
        $totalMinutes = $totalHours * 60 + $differenceInMs->i; // Конвертируем часы в минуты и добавляем минуты

        // Для удобства, если вам нужно вернуть только часы и минуты
        $hoursDiff = floor($totalMinutes / 60);
        $minutesDiff = $totalMinutes % 60;

        return BorderCrossingService::declensionHours($hoursDiff) . ' ' . BorderCrossingService::declensionMinutes($minutesDiff);
    }
}
