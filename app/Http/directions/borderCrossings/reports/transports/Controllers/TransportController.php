<?php

namespace App\Http\directions\borderCrossings\reports\transports\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\reports\Entities\Report;
use App\Http\directions\borderCrossings\reports\Services\ReportService;
use App\Http\directions\borderCrossings\reports\transports\Services\TransportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nette\Schema\ValidationException;


/**
 * @OA\Schema(
 *     schema="Transport",
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
 *         example="Auto"
 *     ),
 *     @OA\Property(
 *          property="icon",
 *          type="string",
 *          example="path"
 *      )
 * )
 */
class TransportController extends Controller
{
    private TransportService $transportService;

    /**
     * @param TransportService $transportService
     */
    public function __construct(TransportService $transportService)
    {
        $this->transportService = $transportService;
    }

    /**
     * @OA\Get(
     *     path="/directions/borderCrossing/reports/transports",
     *     tags={"Transport"},
     *     summary="Получение всех видов ТС",
     *     @OA\Response(
     *       response="200",
     *       description="Request Successful",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/Transport")
     *       )
     *     )
     * )
     */
    public function getAll()
    {
        return response()->json($this->transportService->getAllTransport());
    }
}
