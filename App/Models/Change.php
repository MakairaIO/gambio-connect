<?php

namespace GXModules\Makaira\GambioConnect\App\Models;


class Change
{

    //define table name as constant
    private int    $id;
    private string $gambioId;
    private string $type;
    private string $comment = "";
    private string $created_at;
    private ?string $consumed_at = null;

    public function __construct(int $id, string $gambioid, string $type, string $comment, string $created_at, ?string $consumed_at)
    {
        $this->id       = $id;
        $this->gambioId = $gambioid;
        $this->type     = $type;
        $this->comment = $comment;
        $this->created_at = $created_at;
        $this->consumed_at = $consumed_at;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'gambioid' => $this->gambioId,
            'type' => $this->type,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'consumed_at' => $this->consumed_at,
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
        return $this->gambioId;
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
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getConsumedAt(): string
    {
        return $this->consumed_at;
    }

    public function setConsumedAt(string $consumed_at): void
    {
        $this->consumed_at = $consumed_at;
    }
}
