<?php

namespace App\Http\directions\borderCrossings\reports\DTO;

class StatisticGraphTypeDTO
{
    private $car;
    private $bus;

    /**
     * @param $car
     * @param $bus
     */
    public function __construct($car, $bus)
    {
        $this->car = $car;
        $this->bus = $bus;
    }


    public function getCar(): array
    {
        return $this->car;
    }

    public function setCar(array $car): void
    {
        $this->car = $car;
    }

    public function getBus(): array
    {
        return $this->bus;
    }

    public function setBus(array $bus): void
    {
        $this->bus = $bus;
    }


    public function toArray(): array
    {
        return [
            'car' => $this->car,
            'bus' => $this->bus
        ];
    }

}
