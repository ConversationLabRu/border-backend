<?php

namespace App\Http\directions\borderCrossings\reports\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\reports\Entities\Report;
use App\Http\directions\borderCrossings\reports\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nette\Schema\ValidationException;


/**
 * @OA\Schema(
 *     schema="Report",
 *     type="object",
 *     required={"id", "name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="border_crossing_id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *          property="transport_id",
 *          type="integer",
 *          format="int64",
 *          example=1
 *      ),
 *     @OA\Property(
 *          property="user_id",
 *          type="integer",
 *          format="int64",
 *          example=1
 *      ),
 *     @OA\Property(
 *         property="checkpoint_queue",
 *         type="timestamp",
 *         example="2024-07-01 08:00:00"
 *     ),
 *     @OA\Property(
 *          property="checkpoint_entry",
 *          type="timestamp",
 *          example="2024-07-01 08:00:00"
 *      ),
 *     @OA\Property(
 *          property="checkpoint_exit",
 *          type="timestamp",
 *          example="2024-07-01 08:00:00"
 *      ),
 *     @OA\Property(
 *          property="comment",
 *          type="string",
 *          example="comment"
 *      )
 * )
 */
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

    /**
     * @OA\Get(
     *     path="/api/directions/borderCrossing/reports/last",
     *     tags={"Report"},
     *     summary="Получение последних 6-и отчетов погран-перехода",
     *     @OA\Response(
     *       response="200",
     *       description="Request Successful",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/Report")
     *       )
     *     ),
     *     @OA\Response(
     *        response="400",
     *        description="Ошибка при не переданном GET парметре",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Не передан borderCrossingId")
     *        )
     *      )
     * )
     */
    public function getLastReports(Request $request)
    {
        try {
            return response()->json($this->reportService->getLastReportByBorderCrossing($request));
        } catch (\ArgumentCountError $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/directions/borderCrossing/reports",
     *     tags={"Report"},
     *     summary="Получение всех отчетов погран-перехода",
     *     @OA\Response(
     *       response="200",
     *       description="Request Successful",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/Report")
     *       )
     *     ),
     *     @OA\Response(
     *        response="400",
     *        description="Ошибка при не переданном GET парметре",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Не передан borderCrossingId")
     *        )
     *      )
     * )
     */
    public function index(Request $request)
    {
        try {
            return response()->json($this->reportService->getAllReportByBorderCrossing($request));
        } catch (\ArgumentCountError $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/directions/borderCrossing/reports",
     *     tags={"Report"},
     *     summary="Создать новый отчет",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"border_crossing_id", "transport_id", "user_id", "checkpoint_entry", "checkpoint_exit"},
     *             @OA\Property(
     *                 property="border_crossing_id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="transport_id",
     *                 type="integer",
     *                 example=2
     *             ),
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 example=3
     *             ),
     *             @OA\Property(
     *                 property="checkpoint_queue",
     *                 type="string",
     *                 format="date-time",
     *                 example="2024-07-01T08:00:00Z"
     *             ),
     *             @OA\Property(
     *                 property="checkpoint_entry",
     *                 type="string",
     *                 format="date-time",
     *                 example="2024-07-01T09:00:00Z"
     *             ),
     *             @OA\Property(
     *                 property="checkpoint_exit",
     *                 type="string",
     *                 format="date-time",
     *                 example="2024-07-01T10:00:00Z"
     *             ),
     *             @OA\Property(
     *                 property="comment",
     *                 type="string",
     *                 example="Sample comment"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Report Created Successfully",
     *         @OA\JsonContent(
     *             type="object",
     *          @OA\Property(
     *                   property="id",
     *                   type="integer",
     *                   example=1
     *               ),
     *             @OA\Property(
     *                  property="border_crossing_id",
     *                  type="integer",
     *                  example=1
     *              ),
     *              @OA\Property(
     *                  property="transport_id",
     *                  type="integer",
     *                  example=2
     *              ),
     *              @OA\Property(
     *                  property="user_id",
     *                  type="integer",
     *                  example=3
     *              ),
     *              @OA\Property(
     *                  property="checkpoint_queue",
     *                  type="string",
     *                  format="date-time",
     *                  example="2024-07-01T08:00:00Z"
     *              ),
     *              @OA\Property(
     *                  property="checkpoint_entry",
     *                  type="string",
     *                  format="date-time",
     *                  example="2024-07-01T09:00:00Z"
     *              ),
     *              @OA\Property(
     *                  property="checkpoint_exit",
     *                  type="string",
     *                  format="date-time",
     *                  example="2024-07-01T10:00:00Z"
     *              ),
     *              @OA\Property(
     *                  property="comment",
     *                  type="string",
     *                  example="Sample comment"
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Ошибка если Entity не прошел валидацию",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The transport id field is required.")
     *         )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        try {
            return response()->json($this->reportService->createReport($request), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
