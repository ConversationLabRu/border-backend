<?php

namespace App\Http\directions\Controllers;

use App\Http\Controllers\Controller;
use App\Http\directions\Services\DirectionService;

class DirectionController extends Controller
{
    private DirectionService $directionService;

    /**
     * @param DirectionService $directionService
     */
    public function __construct(DirectionService $directionService)
    {
        $this->directionService = $directionService; // Corrected assignment
    }

    public function getAll()
    {
        return response()->json($this->directionService->getAllDirections());
    }
}
