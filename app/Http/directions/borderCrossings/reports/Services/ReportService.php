<?php

namespace App\Http\directions\borderCrossings\reports\Services;

use App\Http\directions\borderCrossings\reports\Entities\Report;

class ReportService
{
    public function getLastReportByBorderCrossing(int $borderCrossingId)
    {
        return Report::where('border_crossing_id', $borderCrossingId)
            ->orderBy('checkpoint_exit', 'desc') // Сортировка по дате в порядке убывания
            ->limit(6) // Ограничение результата до 6 записей
            ->get(); // Получение результата
    }

    public function getAllReportByBorderCrossing(int $borderCrossingId)
    {
        return Report::where("border_crossing_id", $borderCrossingId)
            ->orderBy('checkpoint_exit', 'desc')
            ->get();
    }

    public function createReport(Report $report)
    {
        $report->save();

        return $report;

    }
}
