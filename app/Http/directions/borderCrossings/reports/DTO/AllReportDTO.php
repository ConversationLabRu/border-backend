<?php

namespace App\Http\directions\borderCrossings\reports\DTO;

use App\Http\directions\borderCrossings\reports\transports\DTO\TransportDTO;

class AllReportDTO extends LastReportDTO
{
    private int $id;
    private TransportDTO $transport;

    /**
     * @param int $id
     * @param TransportDTO $transport
     */
    public function __construct(string $checkpointEntry,
                                string $checkpointExit,
                                ?string $checkpointQueue,
                                ?string $comment,
                                bool $isFlippedDirection,
                                int $id,
                                TransportDTO $transport)
    {
        parent::__construct($checkpointEntry, $checkpointExit, $checkpointQueue, $comment, $isFlippedDirection);
        $this->id = $id;
        $this->transport = $transport;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTransport(): TransportDTO
    {
        return $this->transport;
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
            'transport' => $this->transport->toArray(),
        ];
    }
}
