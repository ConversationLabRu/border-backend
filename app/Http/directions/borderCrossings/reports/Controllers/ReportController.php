<?php

namespace App\Http\directions\borderCrossings\reports\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\reports\Entities\Report;
use App\Http\directions\borderCrossings\reports\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private ReportService $reportService;

    /**
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getLastReports(Request $request)
    {
        $borderCrossingId = (int) $request->query("borderCrossingId");

        return response()->json($this->reportService->getLastReportByBorderCrossing($borderCrossingId));
    }

    public function getAll(Request $request)
    {
        $borderCrossingId = (int) $request->query("borderCrossingId");

        return response()->json($this->reportService->getAllReportByBorderCrossing($borderCrossingId));
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
            'comment'
        ]));

        // Возвращение ответа
        return response()->json($this->reportService->createReport($report), 201); // Возвращает созданную запись с кодом ответа 201
    }
}
