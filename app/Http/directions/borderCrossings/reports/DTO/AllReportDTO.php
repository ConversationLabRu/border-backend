<?php

namespace App\Http\directions\borderCrossings\reports\DTO;

use App\Http\directions\borderCrossings\reports\transports\DTO\TransportDTO;
use App\Http\directions\borderCrossings\reports\transports\Entities\Transport;

class AllReportDTO extends LastReportDTO
{
    private int $id;
    private Transport $transport;

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
                                int $userId)
    {
        parent::__construct($checkpointEntry, $checkpointExit, $checkpointQueue, $comment, $isFlippedDirection, $userId);
        $this->id = $id;
        $this->transport = $transport;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTransport(): Transport
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
            'user_id' => $this->getUserId(),
            'transport' => $this->transport->toArray(),
        ];
    }
}
