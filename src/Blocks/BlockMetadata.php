<?php

namespace Notion\Blocks;

use DateTimeImmutable;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;

/**
 * @psalm-type BlockMetadataJson = array{
 *      type: string,
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      archived: bool,
 *      has_children: bool,
 * }
 *
 * @psalm-immutable
 */
class BlockMetadata
{
    private function __construct(
        public readonly string $id,
        public readonly DateTimeImmutable $createdTime,
        public readonly DateTimeImmutable $lastEditedTime,
        public readonly bool $archived,
        public readonly bool $hasChildren,
        public readonly BlockType $type
    ) {
    }

    /** @internal */
    public static function create(BlockType $type): self
    {
        $now = new DateTimeImmutable("now");

        return new self("", $now, $now, false, false, $type);
    }

    /**
     * @psalm-param BlockMetadataJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = BlockType::tryFrom($array["type"]);
        if ($type === null) {
            throw BlockException::invalidType($array["type"]);
        }

        return new self(
            $array["id"],
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            $array["archived"],
            $array["has_children"],
            $type,
        );
    }

    /** @internal */
    public function toArray(): array
    {
        $array = [
            "object"           => "block",
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "archived"         => $this->archived,
            "has_children"     => $this->hasChildren,
            "type"             => $this->type->value,
        ];

        if ($this->id !== "") {
            $array["id"] = $this->id;
        }

        return $array;
    }

    /** @internal */
    public function archive(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            new DateTimeImmutable("now"),
            true,
            $this->hasChildren,
            $this->type,
        );
    }

    /** @internal */
    public function restore(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            new DateTimeImmutable("now"),
            false,
            $this->hasChildren,
            $this->type,
        );
    }

    /** @internal */
    public function updateHasChildren(bool $hasChildren): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            new DateTimeImmutable("now"),
            $this->archived,
            $hasChildren,
            $this->type,
        );
    }

    public function update(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            new DateTimeImmutable("now"),
            $this->archived,
            $this->hasChildren,
            $this->type,
        );
    }

    /**
     * @internal
     *
     * @throws BlockException
     */
    public function checkType(BlockType $expectedType): void
    {
        if ($this->type !== $expectedType) {
            throw BlockException::wrongType($expectedType);
        }
    }
}
