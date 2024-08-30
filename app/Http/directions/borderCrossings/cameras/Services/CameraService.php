<?php

namespace App\Http\directions\borderCrossings\cameras\Services;

use App\Http\directions\borderCrossings\cameras\Dto\CameraDTO;
use App\Http\directions\borderCrossings\cameras\Entities\Camera;
use App\Utils\LogUtils;
use AWS\CRT\Log;
use Illuminate\Http\Request;

class CameraService
{
    public function getAllByBorderCrossings(Request $request)
    {
        $borderCrossingId = (int) $request->query('borderCrossingId');

        if ($borderCrossingId == 0) throw new \ArgumentCountError("Не передан borderCrossingId");

        LogUtils::elasticLog($request, "Перешел на страницу с Камерами погран-перехода: " . $borderCrossingId);

        $cameras = Camera::all()->where("border_crossing_id", $borderCrossingId);

        $result = $cameras->map(function (Camera $camera) {
            $cameraDTO = new CameraDTO(
                $camera->getAttributeValue("url"),
                $camera->getAttributeValue("description"),
                $camera->getAttributeValue("photo"),
                $camera->getAttributeValue("id"),
            );

            return $cameraDTO->toArray();
        });

        return array_values($result->toArray());
    }
}
