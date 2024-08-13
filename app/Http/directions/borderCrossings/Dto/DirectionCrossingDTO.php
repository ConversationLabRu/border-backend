<?php

namespace App\Http\directions\borderCrossings\Dto;

class DirectionCrossingDTO
{
    private int $id;
    private bool $is_quque;
    private string $header_image;
    private ?CityDTO $from_city;
    private ?CityDTO $to_city;

    public function __construct(int $id, bool $is_quque, string $header_image, ?CityDTO $from_city, ?CityDTO $to_city)
    {
        $this->id = $id;
        $this->is_quque = $is_quque;
        $this->header_image = $header_image;
        $this->from_city = $from_city;
        $this->to_city = $to_city;
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

    // Convert DTO to array for easy JSON serialization
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'is_quque' => $this->is_quque,
            'header_image' => $this->header_image,
            'from_city' => $this->from_city?->toArray(),
            'to_city' => $this->to_city?->toArray(),
        ];
    }
}
