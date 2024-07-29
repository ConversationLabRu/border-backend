<?php

namespace App\Http\directions\Services;

use App\Http\directions\Entities\Direction;

class DirectionService
{
    public function getAllDirections()
    {
        return Direction::all();
    }
}
