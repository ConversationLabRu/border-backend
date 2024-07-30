<?php

namespace App\Http\directions\borderCrossings\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\Services\BorderCrossingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;


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

    public function getAll(Request $request)
    {
        try {
            return response()->json($this->borderCrossingService->getAllBorderCrossings($request));
        } catch (\ArgumentCountError $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}
