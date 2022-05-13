<?php

namespace Notion\Databases\Query;

/** @psalm-immutable */
class DateFilter implements Filter, Condition
{
    private const TYPE_PROPERTY = "property";
    private const TYPE_TIMESTAMP = "timestamp";

    private const OPERATOR_EQUALS = "equals";
    private const OPERATOR_BEFORE = "before";
    private const OPERATOR_AFTER = "after";
    private const OPERATOR_ON_OR_BEFORE = "on_or_before";
    private const OPERATOR_IS_EMPTY = "is_empty";
    private const OPERATOR_IS_NOT_EMPTY = "is_not_empty";
    private const OPERATOR_ON_OR_AFTER = "on_or_after";
    private const OPERATOR_PAST_WEEK = "past_week";
    private const OPERATOR_PAST_MONTH = "past_month";
    private const OPERATOR_PAST_YEAR = "past_year";
    private const OPERATOR_NEXT_WEEK = "next_week";
    private const OPERATOR_NEXT_MONTH = "next_month";
    private const OPERATOR_NEXT_YEAR = "next_year";

    /** @var self::TYPE_* */
    private string $propertyType;
    private string $propertyName;
    /** @var self::OPERATOR_* */
    private string $operator;
    private string|bool|array $value;

    /**
     * @param self::TYPE_* $propertyType
     * @param self::OPERATOR_* $operator
     */
    private function __construct(
        string $propertyType,
        string $propertyName,
        string $operator,
        string|bool|array $value,
    ) {
        $this->propertyType = $propertyType;
        $this->propertyName = $propertyName;
        $this->operator = $operator;
        $this->value = $value;
    }

    public static function property(string $propertyName): self
    {
        return new self(
            self::TYPE_PROPERTY,
            $propertyName,
            self::OPERATOR_IS_NOT_EMPTY,
            true
        );
    }

    public static function createdTime(): self
    {
        return new self(
            self::TYPE_TIMESTAMP,
            "created_time",
            self::OPERATOR_IS_NOT_EMPTY,
            true
        );
    }

    public static function lastEditedTime(): self
    {
        return new self(
            self::TYPE_TIMESTAMP,
            "last_edited_time",
            self::OPERATOR_IS_NOT_EMPTY,
            true
        );
    }

    public function propertyType(): string
    {
        return $this->propertyType;
    }

    public function propertyName(): string
    {
        return $this->propertyName;
    }

    /** @return static::OPERATOR_* */
    public function operator(): string
    {
        return $this->operator;
    }

    public function value(): string|bool|array
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            $this->propertyType() => $this->propertyName,
            "date" => [
                $this->operator => $this->value
            ],
        ];
    }

    public function equals(string $value): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_EQUALS, $value);
    }

    public function before(string $value): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_BEFORE, $value);
    }

    public function after(string $value): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_AFTER, $value);
    }

    public function onOrBefore(string $value): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_ON_OR_BEFORE, $value);
    }

    public function isEmpty(): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_IS_EMPTY, true);
    }

    public function isNotEmpty(): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_IS_NOT_EMPTY, true);
    }

    public function onOrAfter(string $value): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_ON_OR_AFTER, $value);
    }

    public function pastWeek(): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_PAST_WEEK, []);
    }

    public function pastMonth(): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_PAST_MONTH, []);
    }

    public function pastYear(): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_PAST_YEAR, []);
    }

    public function nextWeek(): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_NEXT_WEEK, []);
    }

    public function nextMonth(): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_NEXT_MONTH, []);
    }

    public function nextYear(): self
    {
        return new self($this->propertyType, $this->propertyName, self::OPERATOR_NEXT_YEAR, []);
    }
}
