<?php

namespace App\Http\directions\borderCrossings\Services;

use App\Http\directions\borderCrossings\Dto\CityDTO;
use App\Http\directions\borderCrossings\Dto\CountryDTO;
use App\Http\directions\borderCrossings\Dto\DirectionCrossingDTO;
use App\Http\directions\borderCrossings\Entities\BorderCrossing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Integer;

class BorderCrossingService
{
    public function getAllBorderCrossings(Request $request)
    {
        $directionId = (int) $request->query('directionId');

        if ($directionId == 0) {
            throw new \ArgumentCountError("Не передан directionId");
        }

        // Загружаем данные с отношениями
        $directions = BorderCrossing::with('fromCity.country', 'toCity.country')
            ->where("direction_id", $directionId)
            ->get();

        // Логируем данные для отладки
        Log::info('Loaded directions:', ['directions' => $directions]);

        // Проверка наличия данных
        if ($directions->isEmpty()) {
            Log::info('No directions found for directionId: ' . $directionId);
        }

        $result = $directions->map(function (BorderCrossing $direction) {
            $fromCity = $direction->fromCity;
            $toCity = $direction->toCity;

            if (!$fromCity || !$toCity) {
                Log::warning('Missing city data for direction id: ' . $direction->id);
            }

            $fromCityDTO = $fromCity ? new CityDTO(
                $fromCity->name,
                $fromCity->country ? new CountryDTO(
                    $fromCity->country->name,
                    $fromCity->country->logo
                ) : null
            ) : null;

            $toCityDTO = $toCity ? new CityDTO(
                $toCity->name,
                $toCity->country ? new CountryDTO(
                    $toCity->country->name,
                    $toCity->country->logo
                ) : null
            ) : null;

            $directionDTO = new DirectionCrossingDTO(
                $direction->id,
                $direction->is_quque,
                $direction->header_image,
                $fromCityDTO,
                $toCityDTO
            );

            Log::info('Created DirectionCrossingDTO:', ['directionDTO' => $directionDTO]);

            return $directionDTO->toArray(); // Return as array
        });

        Log::info('Final Result:', ['result' => $result]);

        return $result;
    }

}
