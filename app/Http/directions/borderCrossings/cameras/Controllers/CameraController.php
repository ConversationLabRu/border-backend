<?php

namespace App\Http\directions\borderCrossings\cameras\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\cameras\Services\CameraService;
use Illuminate\Http\Request;

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

    public function getAll(Request $request)
    {
        $borderCrossingId = (int) $request->query('borderCrossingId');

        return response()->json($this->cameraService->getAllByBorderCrossings($borderCrossingId));
    }
}
