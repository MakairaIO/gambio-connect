<?php

namespace GXModules\Makaira\MakairaConnect\App\Models;

class Change
{
    public function __construct(
        private int $id,
        private string $gambioid,
        private string $type,
        private string $created_at,
        private string $comment = "",
        private ?string $consumed_at = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'gambio_id' => $this->getGambioId(),
            'type' => $this->getType(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt(),
            'consumed_at' => $this->getConsumedAt(),
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getGambioId(): string
    {
        return $this->gambioid;
    }

    public function setGambioId(string $gambioId): void
    {
        $this->gambioId = $gambioId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getConsumedAt(): string
    {
        return $this->consumedAt;
    }

    public function setConsumedAt(string $consumedAt): void
    {
        $this->consumedAt = $consumedAt;
    }
}
