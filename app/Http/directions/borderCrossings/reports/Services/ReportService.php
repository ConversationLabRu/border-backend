<?php

namespace App\Http\directions\borderCrossings\reports\Services;

use App\Http\directions\borderCrossings\reports\Entities\Report;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

class ReportService
{
    public function getLastReportByBorderCrossing(Request $request)
    {
        $borderCrossingId = (int) $request->query("borderCrossingId");

        if ($borderCrossingId == 0) throw new \ArgumentCountError("Не передан borderCrossingId");


        return Report::where('border_crossing_id', $borderCrossingId)
            ->orderBy('checkpoint_exit', 'desc') // Сортировка по дате в порядке убывания
            ->limit(6) // Ограничение результата до 6 записей
            ->get(); // Получение результата
    }

    public function getAllReportByBorderCrossing(Request $request)
    {
        $borderCrossingId = (int) $request->query("borderCrossingId");

        if ($borderCrossingId == 0) throw new \ArgumentCountError("Не передан borderCrossingId");

        return Report::with('transport')
            ->where("border_crossing_id", $borderCrossingId)
            ->orderBy('checkpoint_exit', 'desc')
            ->get();
    }

    public function createReport(Request $request)
    {
        // Валидация данных
        $request->validate([
            'border_crossing_id' => 'required|exists:borderсrossings,id',
            'transport_id' => 'required|exists:transports,id',
            'user_id' => 'required|exists:users,id',
            'checkpoint_queue' => 'nullable|date',
            'checkpoint_entry' => 'required|date',
            'checkpoint_exit' => 'required|date',
            'comment' => 'nullable|string',
            'is_flipped_direction' => 'nullable|boolean'
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
            'is_flipped_direction'
        ]));

        $report->save();

        return $report;

    }
}
