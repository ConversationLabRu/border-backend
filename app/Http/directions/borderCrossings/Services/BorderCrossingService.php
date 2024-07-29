<?php

namespace App\Http\directions\borderCrossings\Services;

use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use Ramsey\Uuid\Type\Integer;

class BorderCrossingService
{
    public function getAllBorderCrossings(int $directionId)
    {
        return BorderCrossing::all()->where("direction_id", $directionId);
    }
}
