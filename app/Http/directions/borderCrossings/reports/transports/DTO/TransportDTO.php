<?php

namespace App\Http\directions\borderCrossings\reports\transports\DTO;

class TransportDTO
{
    private string $icon;

    /**
     * @param string $icon
     */
    public function __construct(string $icon)
    {
        $this->icon = $icon;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function toArray()
    {
        return [
            'icon' => $this->icon
        ];
    }
}
