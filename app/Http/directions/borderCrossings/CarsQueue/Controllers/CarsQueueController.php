<?php

namespace App\Http\directions\borderCrossings\CarsQueue\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\CarsQueue\Services\CarsQueueServices;
use App\Http\directions\borderCrossings\Services\BorderCrossingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class CarsQueueController extends Controller
{
    private CarsQueueServices $carsQueueServices;

    /**
     * @param CarsQueueServices $carsQueueServices
     */
    public function __construct(CarsQueueServices $carsQueueServices)
    {
        $this->carsQueueServices = $carsQueueServices;
    }

    public function index(Request $request)
    {
        try {
            return response()->json($this->carsQueueServices->getLastQueueByBorderCrossing($request));
        } catch (\ArgumentCountError $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->carsQueueServices->createQueue($request);
            return response(status: 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
