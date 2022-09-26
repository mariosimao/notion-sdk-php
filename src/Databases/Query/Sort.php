<?php

namespace Notion\Databases\Query;

/**
 * @psalm-immutable
 */
class Sort
{
    private const TYPE_PROPERTY = "property";
    private const TYPE_TIMESTAMP = "timestamp";

    private const ORDER_ASCENDING = "ascending";
    private const ORDER_DESCENDING = "descending";

    /**
     * @psalm-param self::TYPE_* $type
     * @psalm-param self::ORDER_* $direction
     */
    private function __construct(
        private readonly string $type,
        private readonly string $propertyName,
        private readonly string $direction,
    ) {
    }

    public static function property(string $propertyName): self
    {
        return new self(self::TYPE_PROPERTY, $propertyName, self::ORDER_ASCENDING);
    }

    public static function createdTime(): self
    {
        return new self(self::TYPE_TIMESTAMP, "created_time", self::ORDER_ASCENDING);
    }

    public static function lastEditedTime(): self
    {
        return new self(self::TYPE_TIMESTAMP, "last_edited_time", self::ORDER_ASCENDING);
    }

    public function ascending(): self
    {
        return new self($this->type, $this->propertyName, self::ORDER_ASCENDING);
    }

    public function descending(): self
    {
        return new self($this->type, $this->propertyName, self::ORDER_DESCENDING);
    }

    public function toArray(): array
    {
        return [
            $this->type => $this->propertyName,
            "direction" => $this->direction,
        ];
    }
}
