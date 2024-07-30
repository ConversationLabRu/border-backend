<?php

namespace App\Http\directions\borderCrossings\reports\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\reports\Entities\Report;
use App\Http\directions\borderCrossings\reports\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nette\Schema\ValidationException;

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
        try {
            return response()->json($this->reportService->getLastReportByBorderCrossing($request));
        } catch (\ArgumentCountError $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAll(Request $request)
    {
        try {
            return response()->json($this->reportService->getAllReportByBorderCrossing($request));
        } catch (\ArgumentCountError $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    public function createReport(Request $request)
    {
        try {
            return response()->json($this->reportService->createReport($request), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
