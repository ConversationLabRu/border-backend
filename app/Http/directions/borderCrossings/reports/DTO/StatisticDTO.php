<?php

namespace App\Http\directions\borderCrossings\reports\DTO;

class StatisticDTO
{
    private string $timeCarNotFlipped;
    private string $timeCarFlipped;
    private string $timeBusNotFlipped;
    private string $timeBusFlipped;

    /**
     * @param string $timeCarNotFlipped
     * @param string $timeCarFlipped
     * @param string $timeBusNotFlipped
     * @param string $timeBusFlipped
     */
    public function __construct(string $timeCarNotFlipped, string $timeCarFlipped, string $timeBusNotFlipped, string $timeBusFlipped)
    {
        $this->timeCarNotFlipped = $timeCarNotFlipped;
        $this->timeCarFlipped = $timeCarFlipped;
        $this->timeBusNotFlipped = $timeBusNotFlipped;
        $this->timeBusFlipped = $timeBusFlipped;
    }

    public function getTimeCarNotFlipped(): string
    {
        return $this->timeCarNotFlipped;
    }

    public function setTimeCarNotFlipped(string $timeCarNotFlipped): void
    {
        $this->timeCarNotFlipped = $timeCarNotFlipped;
    }

    public function getTimeCarFlipped(): string
    {
        return $this->timeCarFlipped;
    }

    public function setTimeCarFlipped(string $timeCarFlipped): void
    {
        $this->timeCarFlipped = $timeCarFlipped;
    }

    public function getTimeBusNotFlipped(): string
    {
        return $this->timeBusNotFlipped;
    }

    public function setTimeBusNotFlipped(string $timeBusNotFlipped): void
    {
        $this->timeBusNotFlipped = $timeBusNotFlipped;
    }

    public function getTimeBusFlipped(): string
    {
        return $this->timeBusFlipped;
    }

    public function setTimeBusFlipped(string $timeBusFlipped): void
    {
        $this->timeBusFlipped = $timeBusFlipped;
    }

    public function toArray(): array
    {
        return [
            'timeCarNotFlipped' => $this->timeCarNotFlipped,
            'timeCarFlipped' => $this->timeCarFlipped,
            'timeBusNotFlipped' => $this->timeBusNotFlipped,
            'timeBusFlipped' => $this->timeBusFlipped
        ];
    }

    public function __toString(): string
    {
        return "timeCarNotFlipped: " . $this->timeCarNotFlipped . "\n" . $this->timeCarFlipped . "\n" . $this->timeBusNotFlipped . "\n" . $this->timeBusFlipped;
    }


}
