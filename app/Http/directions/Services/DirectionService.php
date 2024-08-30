<?php

namespace App\Http\directions\Services;

use App\Http\directions\Entities\Direction;
use App\Utils\LogUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DirectionService
{
    public function getAllDirections(Request $request)
    {
        LogUtils::elasticLog($request, "Перешел на страницу Направлений");

        return Direction::all();
    }
}
