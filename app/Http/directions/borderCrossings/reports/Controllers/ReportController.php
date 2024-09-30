<?php

namespace App\Http\directions\borderCrossings\reports\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TelegramController;
use App\Http\directions\borderCrossings\reports\Entities\Report;
use App\Http\directions\borderCrossings\reports\Exceptions\TimeExpiredDeletedException;
use App\Http\directions\borderCrossings\reports\Services\ReportService;
use App\Telegram\Handler;
use App\Utils\LogUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nette\Schema\ValidationException;


/**
 * @OA\Schemas (
 *     @OA\Schema(
 *      schema="LastReportDTO",
 *      type="object",
 *      required={"id", "name"},
 *      @OA\Property(
 *          property="checkpoint_queue",
 *          type="string",
 *          example="2024-08-14 13:04:00"
 *      ),
 *      @OA\Property(
 *           property="checkpoint_entry",
 *           type="string",
 *           example="2024-08-14 13:04:00"
 *       ),
 *      @OA\Property(
 *           property="checkpoint_exit",
 *           type="string",
 *           example="2024-08-14 13:04:00"
 *       ),
 *      @OA\Property(
 *            property="comment",
 *            type="string",
 *            example="test"
 *        ),
 *      @OA\Property(
 *            property="is_flipped_direction",
 *            type="boolean",
 *            example=true
 *        )
 *  ),
 *     @OA\Schema(
 *      schema="AllReportDTO",
 *      type="object",
 *      required={"id", "name"},
 *      @OA\Property(
 *          property="checkpoint_queue",
 *          type="string",
 *          example="2024-08-14 13:04:00"
 *      ),
 *      @OA\Property(
 *           property="checkpoint_entry",
 *           type="string",
 *           example="2024-08-14 13:04:00"
 *       ),
 *      @OA\Property(
 *           property="checkpoint_exit",
 *           type="string",
 *           example="2024-08-14 13:04:00"
 *       ),
 *      @OA\Property(
 *            property="comment",
 *            type="string",
 *            example="test"
 *        ),
 *      @OA\Property(
 *            property="is_flipped_direction",
 *            type="boolean",
 *            example=true
 *        ),
 *     @OA\Property(
 *             property="id",
 *             type="integer",
 *             example=1
 *         ),
 *     @OA\Property(
 *             property="transport",
 *             type="object",
 *                  @OA\Property(
 *                  property="icon",
 *                  type="string",
 *                  example="img.png"
 *              )
 *         ),
 *      @OA\Property(
 *                   property="time_enter_waiting_area",
 *                   type="string",
 *                   format="date-time",
 *                   example="2024-07-01T10:00:00Z"
 *               ),
 *               @OA\Property(
 *                   property="time_leave_waiting_area",
 *                   type="string",
 *                   format="date-time",
 *                   example="2024-07-01T10:00:00Z"
 *               ),
 *              @OA\Property(
 *             property="time_difference_text",
 *             type="string",
 *             example="11 часов 6 минут"
 *         ),
 *          ),
 *              @OA\Property(
 *                    property="is_show_button",
 *                    type="boolean",
 *                    example="true"
 *                ),
 *  )
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
     *           @OA\Items(ref="#/components/schemas/LastReportDTO")
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
     *           @OA\Items(ref="#/components/schemas/AllReportDTO")
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
     *             ),
     *              @OA\Property(
     *                  property="is_flipped_direction",
     *                  type="boolean",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="time_enter_waiting_area",
     *                  type="string",
     *                  format="date-time",
     *                  example="2024-07-01T10:00:00Z"
     *              ),
     *              @OA\Property(
     *                  property="time_leave_waiting_area",
     *                  type="string",
     *                  format="date-time",
     *                  example="2024-07-01T10:00:00Z"
     *              ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Report Created Successfully"
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
            $this->reportService->createReport($request);
            $this->reportService->sendReportPostText($request);
            return response(status: 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $this->reportService->deleteReportById($request);
            return response(status: 204);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (TimeExpiredDeletedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function statistics(Request $request)
    {
        try {

            $borderCrossingId = (int) $request->query("borderCrossingId");

            LogUtils::elasticLog($request, "Запросил прогноз по погран-переходу: " . $borderCrossingId);

            $result = $this->reportService->getStatistics($borderCrossingId);

            LogUtils::elasticLog($request, "Результат статистики погран перехода " . $borderCrossingId . ": " . $result);



            return \response()->json($result->toArray());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function statisticsGraph(Request $request)
    {
        try {
            return \response()->json($this->reportService->getStatisticsForGraph($request));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
