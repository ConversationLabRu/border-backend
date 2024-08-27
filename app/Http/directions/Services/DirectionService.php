<?php

namespace App\Http\directions\Services;

use App\Http\directions\Entities\Direction;
use Illuminate\Support\Facades\Log;

class DirectionService
{
    public function getAllDirections()
    {
        Log::info("Получение всех направлений");
        return Direction::all();
    }
}
