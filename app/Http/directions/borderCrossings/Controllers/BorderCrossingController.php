<?php

namespace App\Http\directions\borderCrossings\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\Services\BorderCrossingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;


/**
 * @OA\Schema(
 *     schema="BorderCrossing",
 *     type="object",
 *     required={"id", "name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="direction_id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="from",
 *         type="integer",
 *         format="int64",
 *         example="2"
 *     ),
 *     @OA\Property(
 *          property="to",
 *          type="integer",
 *          format="int64",
 *          example="1"
 *      ),
 *     @OA\Property(
 *           property="is_queue",
 *           type="boolean",
 *           example="true"
 *       )
 * )
 */
class BorderCrossingController extends Controller
{
    private BorderCrossingService $borderCrossingService;

    /**
     * @param BorderCrossingService $borderCrossingService
     */
    public function __construct(BorderCrossingService $borderCrossingService)
    {
        $this->borderCrossingService = $borderCrossingService;
    }

    /**
     * @OA\Get(
     *     path="/api/directions/borderCrossing",
     *     tags={"BorderCrossing"},
     *     summary="Получение всех погран-переходов по направлению",
     *     @OA\Response(
     *       response="200",
     *       description="Request Successful",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/BorderCrossing")
     *       )
     *     ),
     *     @OA\Response(
     *        response="400",
     *        description="Ошибка при не переданном GET парметре",
     *        @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Не передан directionId")
     *        )
     *      )
     * )
     */
    public function index(Request $request)
    {
        try {
            return response()->json($this->borderCrossingService->getAllBorderCrossings($request));
        } catch (\ArgumentCountError $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}
