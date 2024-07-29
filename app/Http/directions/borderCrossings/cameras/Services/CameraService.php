<?php

namespace App\Http\directions\borderCrossings\cameras\Services;

use App\Http\directions\borderCrossings\cameras\Entities\Camera;

class CameraService
{
    public function getAllByBorderCrossings(int $borderCrossingId)
    {
        return Camera::all()->where("border_crossing_id", $borderCrossingId);
    }
}
