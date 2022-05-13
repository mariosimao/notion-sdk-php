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

    /** @var self::TYPE_* */
    private string $type;

    private string $property;

    /** @var self::ORDER_* */
    private string $direction;

    /**
     * @param self::TYPE_* $type
     * @param self::ORDER_* $direction
     */
    private function __construct(string $type, string $property, string $direction)
    {
        $this->type = $type;
        $this->property = $property;
        $this->direction = $direction;
    }

    public static function property(string $property): self
    {
        return new self(self::TYPE_PROPERTY, $property, self::ORDER_ASCENDING);
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
        return new self($this->type, $this->property, self::ORDER_ASCENDING);
    }

    public function descending(): self
    {
        return new self($this->type, $this->property, self::ORDER_DESCENDING);
    }

    public function toArray(): array
    {
        return [
            $this->type => $this->property,
            "direction" => $this->direction,
        ];
    }
}
