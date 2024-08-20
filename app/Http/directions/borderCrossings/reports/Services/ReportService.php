<?php

namespace App\Http\directions\borderCrossings\reports\Services;

use App\Http\directions\borderCrossings\Dto\CityDTO;
use App\Http\directions\borderCrossings\Dto\CountryDTO;
use App\Http\directions\borderCrossings\Dto\DirectionCrossingDTO;
use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use App\Http\directions\borderCrossings\reports\DTO\AllReportDTO;
use App\Http\directions\borderCrossings\reports\DTO\LastReportDTO;
use App\Http\directions\borderCrossings\reports\Entities\Report;
use App\Http\directions\borderCrossings\reports\transports\DTO\TransportDTO;
use Illuminate\Http\Request;
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

        $result = $reports->map(function (Report $report) {

            $reportDTO = new LastReportDTO(
                $report->getAttributeValue("checkpoint_entry"),
                $report->getAttributeValue("checkpoint_exit"),
                $report->getAttributeValue("checkpoint_queue"),
                $report->getAttributeValue("comment"),
                $report->getAttributeValue("is_flipped_direction"),
                $report->getAttributeValue("user_id"),
                $report->getAttributeValue("time_enter_waiting_area"),
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
            ->orderBy('id', 'desc')
            ->get();

        $result = $reports->map(function (Report $report) {

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

        $id = $validatedData['id'];

        $report = Report::find($id);

        if ($report) {
            $report->delete();
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
            'time_enter_waiting_area',
        ]));

        $report->save();

        return $report;

    }
}
