<?php

namespace Notion\Blocks;

use DateTimeImmutable;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;
use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type BlockMetadataJson = array{
 *      type: string,
 *      id: string,
 *      created_time: string,
 *      last_edited_time: string,
 *      created_by?: UserJson,
 *      last_edited_by?: UserJson,
 *      in_trash: bool,
 *      has_children: bool,
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
        public User|null $createdBy,
        public User|null $lastEditedBy,
        public bool $inTrash,
        public bool $hasChildren,
        public BlockType $type,
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

        return new self("", $now, $now, null, null, false, false, $type);
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
            isset($array["created_by"]) ? User::fromArray($array["created_by"]) : null,
            isset($array["last_edited_by"]) ? User::fromArray($array["last_edited_by"]) : null,
            $array["in_trash"],
            $array["has_children"],
            $type,
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

        if ($this->createdBy !== null) {
            $array["created_by"] = $this->createdBy->toArray();
        }

        if ($this->lastEditedBy !== null) {
            $array["last_edited_by"] = $this->lastEditedBy->toArray();
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
            $this->createdBy,
            $this->lastEditedBy,
            true,
            $this->hasChildren,
            $this->type,
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
            $this->createdBy,
            $this->lastEditedBy,
            false,
            $this->hasChildren,
            $this->type,
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
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $hasChildren,
            $this->type,
            $this->unknownType,
        );
    }

    public function update(): self
    {
        return new self(
            $this->id,
            $this->createdTime,
            new DateTimeImmutable("now"),
            $this->createdBy,
            $this->lastEditedBy,
            $this->inTrash,
            $this->hasChildren,
            $this->type,
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
