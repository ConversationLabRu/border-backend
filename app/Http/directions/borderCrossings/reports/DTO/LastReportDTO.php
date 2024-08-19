<?php

namespace App\Http\directions\borderCrossings\reports\DTO;

class LastReportDTO
{
    private string $checkpointEntry;
    private string $checkpointExit;
    private ?string $checkpointQueue;
    private ?string $comment;
    private bool $isFlippedDirection;
    private int $userId;

    /**
     * @param string $checkpointEntry
     * @param string $checkpointExit
     * @param string|null $checkpointQueue
     * @param string|null $comment
     * @param bool $isFlippedDirection
     */
    public function __construct(string $checkpointEntry, string $checkpointExit, ?string $checkpointQueue, ?string $comment, bool $isFlippedDirection, int $userId)
    {
        $this->checkpointEntry = $checkpointEntry;
        $this->checkpointExit = $checkpointExit;
        $this->checkpointQueue = $checkpointQueue;
        $this->comment = $comment;
        $this->isFlippedDirection = $isFlippedDirection;
        $this->userId = $userId;
    }

    public function getCheckpointEntry(): string
    {
        return $this->checkpointEntry;
    }

    public function getCheckpointExit(): string
    {
        return $this->checkpointExit;
    }

    public function getCheckpointQueue(): ?string
    {
        return $this->checkpointQueue;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function isFlippedDirection(): bool
    {
        return $this->isFlippedDirection;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }



    public function toArray(): array
    {
        return [
            'checkpoint_queue' => $this->checkpointQueue,
            'checkpoint_entry' => $this->checkpointEntry,
            'checkpoint_exit' => $this->checkpointExit,
            'comment' => $this->comment,
            'is_flipped_direction' => $this->isFlippedDirection,
            'user_id' => $this->userId
        ];
    }
}
