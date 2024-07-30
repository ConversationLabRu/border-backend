<?php

namespace App\Http\directions\borderCrossings\cameras\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\borderCrossings\cameras\Services\CameraService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        try {
            return response()->json($this->cameraService->getAllByBorderCrossings($request));
        } catch (\ArgumentCountError $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);

        }
    }
}
