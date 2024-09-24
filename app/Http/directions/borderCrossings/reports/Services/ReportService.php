<?php

namespace App\Http\directions\borderCrossings\reports\Services;

use App\Http\directions\borderCrossings\Dto\CityDTO;
use App\Http\directions\borderCrossings\Dto\CountryDTO;
use App\Http\directions\borderCrossings\Dto\DirectionCrossingDTO;
use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use App\Http\directions\borderCrossings\reports\DTO\AllReportDTO;
use App\Http\directions\borderCrossings\reports\DTO\LastReportDTO;
use App\Http\directions\borderCrossings\reports\DTO\StatisticDTO;
use App\Http\directions\borderCrossings\reports\DTO\StatisticGraphDTO;
use App\Http\directions\borderCrossings\reports\DTO\StatisticGraphTypeDTO;
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

    public function getStatisticsForGraph(Request $request)
    {
        $borderCrossingId = (int) $request->query("borderCrossingId");
        LogUtils::elasticLog($request, "Запросил статистику по напрвлению " . $borderCrossingId);

        $sevenDaysAgo = Carbon::now()->subDays(7);
        $currentDate = Carbon::now();

        $averageTimesPerDayCar = Report::where('transport_id', 2)
            ->where('is_flipped_direction', false)
            ->where('border_crossing_id', $borderCrossingId)
            ->whereBetween('checkpoint_exit', [$sevenDaysAgo, $currentDate])
            ->selectRaw('
        DATE(checkpoint_exit) as day,
        AVG(
            ABS(
                CASE
                    WHEN checkpoint_queue IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, checkpoint_queue, checkpoint_exit)
                    WHEN time_enter_waiting_area IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, time_enter_waiting_area, checkpoint_exit)
                    ELSE TIMESTAMPDIFF(MINUTE, checkpoint_entry, checkpoint_exit)
                END
            )
        ) as avg_time')
            ->groupBy('day')
            ->get()
            ->toArray();

        $averageTimesPerDayBus = Report::where('transport_id', 3)
            ->where('is_flipped_direction', false)
            ->where('border_crossing_id', $borderCrossingId)
            ->whereBetween('checkpoint_exit', [$sevenDaysAgo, $currentDate])
            ->selectRaw('
        DATE(checkpoint_exit) as day,
        AVG(
            ABS(
                CASE
                    WHEN checkpoint_queue IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, checkpoint_queue, checkpoint_exit)
                    WHEN time_enter_waiting_area IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, time_enter_waiting_area, checkpoint_exit)
                    ELSE TIMESTAMPDIFF(MINUTE, checkpoint_entry, checkpoint_exit)
                END
            )
        ) as avg_time')
            ->groupBy('day')
            ->get()
            ->toArray();


        $averageTimesPerDayFlippedCar = Report::where('transport_id', 2)
            ->where('is_flipped_direction', true)
            ->where('border_crossing_id', $borderCrossingId)
            ->whereBetween('checkpoint_exit', [$sevenDaysAgo, $currentDate])
            ->selectRaw('
        DATE(checkpoint_exit) as day,
        AVG(
            ABS(
                CASE
                    WHEN checkpoint_queue IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, checkpoint_queue, checkpoint_exit)
                    WHEN time_enter_waiting_area IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, time_enter_waiting_area, checkpoint_exit)
                    ELSE TIMESTAMPDIFF(MINUTE, checkpoint_entry, checkpoint_exit)
                END
            )
        ) as avg_time')
            ->groupBy('day')
            ->get()
            ->toArray();

        $averageTimesPerDayFlippedBus = Report::where('transport_id', 3)
            ->where('is_flipped_direction', true)
            ->where('border_crossing_id', $borderCrossingId)
            ->whereBetween('checkpoint_exit', [$sevenDaysAgo, $currentDate])
            ->selectRaw('
        DATE(checkpoint_exit) as day,
        AVG(
            ABS(
                CASE
                    WHEN checkpoint_queue IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, checkpoint_queue, checkpoint_exit)
                    WHEN time_enter_waiting_area IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, time_enter_waiting_area, checkpoint_exit)
                    ELSE TIMESTAMPDIFF(MINUTE, checkpoint_entry, checkpoint_exit)
                END
            )
        ) as avg_time')
            ->groupBy('day')
            ->get()
            ->toArray();

        $result = new StatisticGraphDTO(
            new StatisticGraphTypeDTO($averageTimesPerDayCar, $averageTimesPerDayBus),
            new StatisticGraphTypeDTO($averageTimesPerDayFlippedCar, $averageTimesPerDayFlippedBus)
        );

        return $result->toArray();


    }

    public function getStatistics(int $borderCrossingId) : StatisticDTO
    {
        $currentDayOfWeek = date('N'); // Получаем текущий день недели (1 = Пн, 7 = Вс)

        $transportData = [
            ['transport_id' => 2, 'is_flipped_direction' => false, 'label' => 'CarNotFlipped'],
            ['transport_id' => 2, 'is_flipped_direction' => true, 'label' => 'CarFlipped'],
            ['transport_id' => 3, 'is_flipped_direction' => false, 'label' => 'BusNotFlipped'],
            ['transport_id' => 3, 'is_flipped_direction' => true, 'label' => 'BusFlipped'],
        ];

        $results = [];

        foreach ($transportData as $data) {
            $reports = $this->getReports($borderCrossingId, $data['transport_id'], $data['is_flipped_direction']);

            // Если недостаточно данных, ищем по текущему дню недели
            if ($reports->count() < 3) {
                $reports = $this->getReportsByDayOfWeek($borderCrossingId, $data['transport_id'], $data['is_flipped_direction'], $currentDayOfWeek);
            }

            $results[$data['label']] = $this->calculateMedian(
                $reports->map(fn($report) => $this->calculatePassageTime($report))->filter()->toArray()
            );
        }

        $result = new StatisticDTO(
            $results['CarNotFlipped'] ?? 'Нет информации',
            $results['CarFlipped'] ?? 'Нет информации',
            $results['BusNotFlipped'] ?? 'Нет информации',
            $results['BusFlipped'] ?? 'Нет информации'
        );

        return $result;
    }

    private function getReports($borderCrossingId, $transportId, $isFlippedDirection)
    {
        return Report::where('border_crossing_id', $borderCrossingId)
            ->where('transport_id', $transportId)
            ->where('is_flipped_direction', $isFlippedDirection)
            ->orderBy('checkpoint_exit', 'desc')
            ->limit(4)
            ->get();
    }

    private function getReportsByDayOfWeek($borderCrossingId, $transportId, $isFlippedDirection, $dayOfWeek)
    {
        return Report::where('border_crossing_id', $borderCrossingId)
            ->where('transport_id', $transportId)
            ->where('is_flipped_direction', $isFlippedDirection)
            ->whereRaw('DAYOFWEEK(checkpoint_exit) = ?', [$dayOfWeek])
            ->orderBy('checkpoint_exit', 'desc')
            ->limit(10)
            ->get();
    }

    private function calculatePassageTime($report)
    {
        $entryTime = new DateTime($report["checkpoint_entry"], new DateTimeZone('Europe/Minsk'));
        $exitTime = new DateTime($report["checkpoint_exit"], new DateTimeZone('Europe/Minsk'));

        $difference = $exitTime->diff($entryTime);

        if (!is_null($report["time_enter_waiting_area"])) {
            $enterWaitingAreaTime = new DateTime($report["time_enter_waiting_area"], new DateTimeZone('Europe/Minsk'));
            $difference = $exitTime->diff($enterWaitingAreaTime);
        }

        if (!is_null($report["checkpoint_queue"])) {
            $queueTime = new DateTime($report["checkpoint_queue"], new DateTimeZone('Europe/Minsk'));
            $difference = $exitTime->diff($queueTime);
        }

        // Получаем разницу в минутах
        return ($difference->days * 24 * 60) + ($difference->h * 60) + $difference->i;
    }

    private function calculateMedian($times)
    {
        if (empty($times)) {
            return "Нет информации";
        }

        sort($times);
        $count = count($times);
        $middle = floor(($count - 1) / 2);

        if ($count % 2 !== 0) {
            $result = $times[$middle];
        } else {
            $result = ($times[$middle] + $times[$middle + 1]) / 2.0;
        }

        $hours = floor($result / 60);
        $minutes = $result % 60;

        return BorderCrossingService::declensionHours($hours) . ' ' . BorderCrossingService::declensionMinutes($minutes);
    }
}
