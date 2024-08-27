<?php

namespace App\Http\directions\borderCrossings\Dto;

class DirectionCrossingDTO
{
    private int $id;
    private bool $is_quque;
    private string $header_image;
    private ?CityDTO $from_city;
    private ?CityDTO $to_city;
    private string $url_arcticle;
    private bool $is_bus;
    private bool $is_walking;
    private bool $is_car;
    private $cache;

    public function __construct(int $id, bool $is_quque, string $header_image, ?CityDTO $from_city, ?CityDTO $to_city, string $url_arcticle, bool $is_bus, bool $is_walking, bool $is_car)
    {
        $this->id = $id;
        $this->is_quque = $is_quque;
        $this->header_image = $header_image;
        $this->from_city = $from_city;
        $this->to_city = $to_city;
        $this->url_arcticle = $url_arcticle;
        $this->is_bus = $is_bus;
        $this->is_walking = $is_walking;
        $this->is_car = $is_car;
    }

    // Public getter methods
    public function getId(): int
    {
        return $this->id;
    }

    public function getIsQuque(): bool
    {
        return $this->is_quque;
    }

    public function getHeaderImage(): string
    {
        return $this->header_image;
    }

    public function getFromCity(): ?CityDTO
    {
        return $this->from_city;
    }

    public function getToCity(): ?CityDTO
    {
        return $this->to_city;
    }

    public function getUrlArcticle(): string
    {
        return $this->url_arcticle;
    }

    public function isIsBus(): bool
    {
        return $this->is_bus;
    }

    public function isIsWalking(): bool
    {
        return $this->is_walking;
    }

    public function isIsCar(): bool
    {
        return $this->is_car;
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param mixed $cache
     */
    public function setCache($cache): void
    {
        $this->cache = $cache;
    }





    // Convert DTO to array for easy JSON serialization
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'is_quque' => $this->is_quque,
            'header_image' => $this->header_image,
            'from_city' => $this->from_city?->toArray(),
            'to_city' => $this->to_city?->toArray(),
            'url_arcticle' => $this->url_arcticle,
            'is_car' => $this->is_car,
            'is_bus' => $this->is_bus,
            'is_walking' => $this->is_walking,
            'cache' => $this->cache,
        ];
    }
}
