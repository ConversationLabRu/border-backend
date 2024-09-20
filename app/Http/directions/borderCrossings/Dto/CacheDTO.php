<?php

namespace App\Http\directions\borderCrossings\Dto;

class CacheDTO
{
    private string $timeFormatString;
    private int $countCar;

    /**
     * @param string $time
     * @param int $countCar
     */
    public function __construct(string $time, int $countCar)
    {
        $this->timeFormatString = $time;
        $this->countCar = $countCar;
    }

    public function getTime(): string
    {
        return $this->timeFormatString;
    }

    public function getCountCar(): int
    {
        return $this->countCar;
    }


    // Convert DTO to array for easy JSON serialization
    public function toArray(): array
    {
        return [
            'time' => $this->timeFormatString,
            'countCar' => $this->countCar,
        ];
    }

}
