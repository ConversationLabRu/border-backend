<?php

namespace App\Http\directions\borderCrossings\Dto;

class CityDTO
{
    private string $name;
    private ?CountryDTO $country;

    public function __construct(string $name, ?CountryDTO $country)
    {
        $this->name = $name;
        $this->country = $country;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): ?CountryDTO
    {
        return $this->country;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'country' => $this->country?->toArray(),
        ];
    }
}
