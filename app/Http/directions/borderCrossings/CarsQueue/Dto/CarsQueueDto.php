<?php

namespace App\Http\directions\borderCrossings\CarsQueue\Dto;

class CarsQueueDto
{
    private string $carCount;
    private string $carCountReverse;

    /**
     * @param string $carCount
     * @param string $carCountReverse
     */
    public function __construct(string $carCount, string $carCountReverse)
    {
        $this->carCount = $carCount;
        $this->carCountReverse = $carCountReverse;
    }

    public function getCarCount(): string
    {
        return $this->carCount;
    }

    public function getCarCountReverse(): string
    {
        return $this->carCountReverse;
    }

    public function toArray(): array
    {
        return [
            'carCount' => $this->carCount,
            'carCountReverse' => $this->carCountReverse,
        ];
    }
}
