<?php

namespace App\Http\directions\borderCrossings\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\Services\BorderCrossingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;


/**
 * @OA\Schema(
 *     schema="CarsQueue",
 *     type="object",
 *     required={"id", "name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="is_queue",
 *         type="boolean",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="header_image",
 *         type="string",
 *         example="test.png"
 *     ),
 *     @OA\Property(
 *         property="from_city",
 *         type="object",
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="City1"
 *         ),
 *         @OA\Property(
 *             property="country",
 *             type="object",
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 example="Country1"
 *             ),
 *             @OA\Property(
 *                 property="logo",
 *                 type="string",
 *                 example="img.png"
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="to_city",
 *         type="object",
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="City2"
 *         ),
 *         @OA\Property(
 *             property="country",
 *             type="object",
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 example="Country2"
 *             ),
 *             @OA\Property(
 *                 property="logo",
 *                 type="string",
 *                 example="img.png"
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *          property="url_arcticle",
 *          type="string",
 *          example="www.google.com"
 *      ),
 *     @OA\Property(
 *          property="cache",
 *          type="object",
 *          @OA\Property(
 *              property="time",
 *              type="string",
 *              example="2024-01-01 14:00:00"
 *          ),
 *          @OA\Property(
 *               property="countCar",
 *               type="int",
 *               example="6"
 *           ),
 *      ),
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
     *     tags={"CarsQueue"},
     *     summary="Получение всех погран-переходов по направлению",
     *     @OA\Response(
     *       response="200",
     *       description="Request Successful",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/CarsQueue")
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
