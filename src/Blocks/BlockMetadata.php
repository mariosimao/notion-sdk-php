<?php

namespace Notion\Blocks;

use DateTimeImmutable;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;

/**
 * @psalm-import-type BlockParentJson from BlockParent
 *
 * @psalm-type BlockMetadataJson = array{
 *      type: string,
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      in_trash: bool,
 *      has_children: bool,
 *      parent?: BlockParentJson,
 * }
 *
 * @psalm-immutable
 */
final readonly class BlockMetadata
{
    private function __construct(
        public string $id,
        public DateTimeImmutable $createdTime,
        public DateTimeImmutable $lastEditedTime,
        public bool $inTrash,
        public bool $hasChildren,
        public BlockType $type,
        public BlockParent|null $parent = null,
        private string|null $unknownType = null
    ) {
        /** @psalm-suppress DeprecatedProperty */
        $this->archived = $inTrash;
    }

    /**
     * @deprecated 1.17.0 Use `$inTrash` instead.
     * @codeCoverageIgnore
     */
    public bool $archived;

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
        $type = BlockType::tryFrom($array["type"]) ?? BlockType::Unknown;

        return new self(
            $array["id"],
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            $array["in_trash"],
            $array["has_children"],
            $type,
            isset($array["parent"]) ? BlockParent::fromArray($array["parent"]) : null,
            $type === BlockType::Unknown ? $array["type"] : null,
        );
    }

    /** @internal */
    public function toArray(): array
    {
        $type = $this->type !== BlockType::Unknown ? $this->type->value : $this->unknownType;

        $array = [
            "object"           => "block",
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "in_trash"         => $this->inTrash,
            "has_children"     => $this->hasChildren,
            "type"             => $type,
        ];

        if ($this->id !== "") {
            $array["id"] = $this->id;
        }

        if ($this->parent !== null) {
            $array["parent"] = $this->parent->toArray();
        }

        return $array;
    }

    /** @internal */
    public function delete(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            new DateTimeImmutable("now"),
            true,
            $this->hasChildren,
            $this->type,
            $this->parent,
            $this->unknownType,
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
            $this->parent,
            $this->unknownType,
        );
    }

    /** @internal */
    public function updateHasChildren(bool $hasChildren): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            new DateTimeImmutable("now"),
            $this->inTrash,
            $hasChildren,
            $this->type,
            $this->parent,
            $this->unknownType,
        );
    }

    public function update(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            new DateTimeImmutable("now"),
            $this->inTrash,
            $this->hasChildren,
            $this->type,
            $this->parent,
            $this->unknownType,
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
