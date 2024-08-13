<?php

namespace App\Http\directions\borderCrossings\Dto;

class CountryDTO
{
    private string $name;
    private string $logo;

    public function __construct(string $name, string $logo)
    {
        $this->name = $name;
        $this->logo = $logo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'logo' => $this->logo,
        ];
    }
}
