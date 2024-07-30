<?php

namespace App\Http\directions\borderCrossings\cameras\Services;

use App\Http\directions\borderCrossings\cameras\Entities\Camera;
use Illuminate\Http\Request;

class CameraService
{
    public function getAllByBorderCrossings(Request $request)
    {
        $borderCrossingId = (int) $request->query('borderCrossingId');

        if ($borderCrossingId == 0) throw new \ArgumentCountError("Не передан borderCrossingId");

        return Camera::all()->where("border_crossing_id", $borderCrossingId);
    }
}
