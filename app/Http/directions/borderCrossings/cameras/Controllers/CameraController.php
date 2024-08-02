<?php

namespace App\Http\directions\borderCrossings\cameras\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\cameras\Services\CameraService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


/**
 * @OA\Schema(
 *     schema="Camera",
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
 *         property="url",
 *         type="string",
 *         example="https://www.camera.ru"
 *     ),
 *     @OA\Property(
 *          property="description",
 *          type="string",
 *          example="Desc"
 *      )
 * )
 */
class CameraController extends Controller
{
    private CameraService $cameraService;

    /**
     * @param CameraService $cameraService
     */
    public function __construct(CameraService $cameraService)
    {
        $this->cameraService = $cameraService;
    }

    /**
     * @OA\Get(
     *     path="/api/directions/borderCrossing/cameras",
     *     tags={"Cameras"},
     *     summary="Получение всех камер по погран-переходу",
     *     @OA\Response(
     *       response="200",
     *       description="Request Successful",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/Camera")
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
    public function getAll(Request $request)
    {
        try {
            return response()->json($this->cameraService->getAllByBorderCrossings($request));
        } catch (\ArgumentCountError $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);

        }
    }
}
