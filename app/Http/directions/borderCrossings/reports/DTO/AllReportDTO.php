<?php

namespace App\Http\directions\borderCrossings\reports\DTO;

use App\Http\directions\borderCrossings\reports\transports\DTO\TransportDTO;
use App\Http\directions\borderCrossings\reports\transports\Entities\Transport;

class AllReportDTO extends LastReportDTO
{
    private int $id;
    private Transport $transport;
    private bool $isShowButton;

    /**
     * @param int $id
     * @param Transport $transport
     */
    public function __construct(string $checkpointEntry,
                                string $checkpointExit,
                                ?string $checkpointQueue,
                                ?string $comment,
                                bool $isFlippedDirection,
                                int $id,
                                Transport $transport,
                                int $userId,
                                ?string $timeEnterWaitingArea,
                                ?string $timeDifferenceText,
                                bool $isShowButton)
    {
        parent::__construct($checkpointEntry, $checkpointExit, $checkpointQueue, $comment, $isFlippedDirection, $userId, $timeEnterWaitingArea, $timeDifferenceText);
        $this->id = $id;
        $this->transport = $transport;
        $this->isShowButton = $isShowButton;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTransport(): Transport
    {
        return $this->transport;
    }

    public function isShowButton(): bool
    {
        return $this->isShowButton;
    }





    public function toArray(): array
    {
        return [
            'checkpoint_queue' => $this->getCheckpointQueue(),
            'checkpoint_entry' => $this->getCheckpointEntry(),
            'checkpoint_exit' => $this->getCheckpointExit(),
            'comment' => $this->getComment(),
            'is_flipped_direction' => $this->isFlippedDirection(),
            'id' => $this->id,
            'user_id' => $this->getUserId(),
            'transport' => $this->transport->toArray(),
            'time_enter_waiting_area' => $this->getTimeEnterWaitingArea(),
            'time_difference_text' => $this->getTimeDifferenceText(),
            'is_show_button' => $this->isShowButton()
        ];
    }
}
