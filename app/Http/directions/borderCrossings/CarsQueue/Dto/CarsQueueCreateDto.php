<?php

namespace App\Http\directions\borderCrossings\CarsQueue\Dto;

class CarsQueueCreateDto
{
    private int $borderCrossingId;
    private int $count;
    private bool $routeReverse;

    /**
     * @param int $borderCrossingId
     * @param int $count
     * @param bool $routeReverse
     */
    public function __construct(int $borderCrossingId, int $count, bool $routeReverse)
    {
        $this->borderCrossingId = $borderCrossingId;
        $this->count = $count;
        $this->routeReverse = $routeReverse;
    }

    public function getBorderCrossingId(): int
    {
        return $this->borderCrossingId;
    }

    public function setBorderCrossingId(int $borderCrossingId): void
    {
        $this->borderCrossingId = $borderCrossingId;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function isRouteReverse(): bool
    {
        return $this->routeReverse;
    }

    public function setRouteReverse(bool $routeReverse): void
    {
        $this->routeReverse = $routeReverse;
    }


}
