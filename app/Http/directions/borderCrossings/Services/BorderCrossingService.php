<?php

namespace App\Http\directions\borderCrossings\Services;

use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class BorderCrossingService
{
    public function getAllBorderCrossings(Request $request)
    {
        $directionId = (int) $request->query('directionId');

        if ($directionId == 0) throw new \ArgumentCountError("Не передан directionId");

        return BorderCrossing::all()->where("direction_id", $directionId);
    }
}
