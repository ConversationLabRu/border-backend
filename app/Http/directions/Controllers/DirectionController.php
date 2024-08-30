<?php

namespace App\Http\directions\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\Services\DirectionService;
use App\Http\directions\Entities\Direction;
use Illuminate\Http\Request;

// Импортируйте вашу модель

/**
 * @OA\Schema(
 *     schema="Direction",
 *     type="object",
 *     required={"id", "name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="North"
 *     ),
 *     @OA\Property(
 *         property="logo",
 *         type="string",
 *         example="path to logo"
 *     ),
 *     @OA\Property(
 *          property="image",
 *          type="string",
 *          example="path to image"
 *      ),
 *     @OA\Property(
 *           property="info",
 *           type="string",
 *           example="info"
 *       )
 * )
 */
class DirectionController extends Controller
{
    private DirectionService $directionService;

    /**
     * @param DirectionService $directionService
     */
    public function __construct(DirectionService $directionService)
    {
        $this->directionService = $directionService;
    }

    /**
     * @OA\Get(
     *     path="/api/directions",
     *     tags={"Direction"},
     *     summary="Получение всех направлений",
     *     @OA\Response(
     *       response="200",
     *       description="Request Successful",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/Direction")
     *       )
     *     )
     * )
     */
    public function index(Request $request)
    {
        return response()->json($this->directionService->getAllDirections($request));
    }
}
