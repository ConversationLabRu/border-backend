<?php

namespace App\Http\directions\borderCrossings\reports\transports\DTO;

class TransportDTO
{
    private string $icon;
    private int $id;

    /**
     * @param string $icon
     * @param int $id
     */
    public function __construct(string $icon, int $id)
    {
        $this->icon = $icon;
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }


    public function toArray()
    {
        return [
            'icon' => $this->icon,
            'id' => $this->id
        ];
    }
}
