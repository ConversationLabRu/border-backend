<?php

namespace App\Http\directions\borderCrossings\reports\DTO;

use PhpParser\Node\Expr\Array_;

class StatisticGraphDTO
{
    private StatisticGraphTypeDTO $timeWeekTo;
    private StatisticGraphTypeDTO $timeWeekToFlip;

    /**
     * @param StatisticGraphTypeDTO $timeWeekTo
     * @param StatisticGraphTypeDTO $timeWeekToFlip
     */
    public function __construct(StatisticGraphTypeDTO $timeWeekTo, StatisticGraphTypeDTO $timeWeekToFlip)
    {
        $this->timeWeekTo = $timeWeekTo;
        $this->timeWeekToFlip = $timeWeekToFlip;
    }

    public function getTimeWeekTo(): StatisticGraphTypeDTO
    {
        return $this->timeWeekTo;
    }

    public function setTimeWeekTo(StatisticGraphTypeDTO $timeWeekTo): void
    {
        $this->timeWeekTo = $timeWeekTo;
    }

    public function getTimeWeekToFlip(): StatisticGraphTypeDTO
    {
        return $this->timeWeekToFlip;
    }

    public function setTimeWeekToFlip(StatisticGraphTypeDTO $timeWeekToFlip): void
    {
        $this->timeWeekToFlip = $timeWeekToFlip;
    }


    public function toArray(): array
    {
        return [
            'timeWeekTo' => $this->timeWeekTo->toArray(),
            'timeWeekToFlip' => $this->timeWeekToFlip->toArray()
        ];
    }
}
