<?php

namespace App\Http\directions\borderCrossings\Dto;

class CachePolandDTO
{
    private string $timeBusFormatString;
    private string $timeAutoFormatString;
    private string $timeUpdate;

    /**
     * @param string $timeBusFormatString
     * @param string $timeAutoFormatString
     * @param string $timeUpdate
     */
    public function __construct(string $timeBusFormatString, string $timeAutoFormatString, string $timeUpdate)
    {
        $this->timeBusFormatString = $timeBusFormatString;
        $this->timeAutoFormatString = $timeAutoFormatString;
        $this->timeUpdate = $timeUpdate;
    }

    public function getTimeBusFormatString(): string
    {
        return $this->timeBusFormatString;
    }

    public function getTimeAutoFormatString(): string
    {
        return $this->timeAutoFormatString;
    }

    public function getTimeUpdate(): string
    {
        return $this->timeUpdate;
    }

    // Convert DTO to array for easy JSON serialization
    public function toArray(): array
    {
        return [
            'timeBusFormatString' => $this->timeBusFormatString,
            'timeCarFormatString' => $this->timeAutoFormatString,
            'timeUpdate' => $this->timeUpdate,
        ];
    }


}
