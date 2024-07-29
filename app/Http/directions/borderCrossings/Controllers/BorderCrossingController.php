<?php

namespace App\Http\directions\borderCrossings\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\Services\BorderCrossingService;
use Illuminate\Http\Request;


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
        $directionId = (int) $request->query('directionId');

        return response()->json($this->borderCrossingService->getAllBorderCrossings($directionId));
    }

}
