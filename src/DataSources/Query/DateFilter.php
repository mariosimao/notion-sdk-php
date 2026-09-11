<?php

namespace Notion\DataSources\Query;

use DateTimeImmutable;
use Notion\Common\Date;
use stdClass;

/** @psalm-immutable */
final readonly class DateFilter implements Filter, Condition
{
    private const TYPE_PROPERTY = "property";
    private const TYPE_TIMESTAMP = "timestamp";

    private const VALID_OPERATORS = [
        Operator::Equals,
        Operator::Before,
        Operator::After,
        Operator::OnOrBefore,
        Operator::IsEmpty,
        Operator::IsNotEmpty,
        Operator::OnOrAfter,
        Operator::PastWeek,
        Operator::PastMonth,
        Operator::PastYear,
        Operator::NextWeek,
        Operator::NextMonth,
        Operator::NextYear,
        Operator::ThisWeek,
    ];

    /**
     * @psalm-param self::TYPE_* $propertyType
     */
    private function __construct(
        private string $propertyType,
        private string $propertyName,
        private Operator $operator,
        private DateTimeImmutable|bool|stdClass $value,
    ) {
        if (!in_array($operator, self::VALID_OPERATORS)) {
            throw new \Exception("Invalid operator");
        }
    }

    public static function property(string $propertyName): self
    {
        return new self(
            self::TYPE_PROPERTY,
            $propertyName,
            Operator::IsNotEmpty,
            true
        );
    }

    public static function createdTime(): self
    {
        return new self(
            self::TYPE_TIMESTAMP,
            "created_time",
            Operator::IsNotEmpty,
            true
        );
    }

    public static function lastEditedTime(): self
    {
        return new self(
            self::TYPE_TIMESTAMP,
            "last_edited_time",
            Operator::IsNotEmpty,
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

    public function operator(): Operator
    {
        return $this->operator;
    }

    public function value(): DateTimeImmutable|bool|stdClass
    {
        return $this->value;
    }

    public function toArray(): array
    {
        $type = $this->propertyType === self::TYPE_PROPERTY ? "date" : $this->propertyName;
        $value = $this->value instanceof DateTimeImmutable
            ? $this->value->format(Date::FORMAT)
            : $this->value;

        return [
            $this->propertyType() => $this->propertyName,
            $type => [
                $this->operator->value => $value,
            ],
        ];
    }

    public function equals(DateTimeImmutable $value): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::Equals, $value);
    }

    public function before(DateTimeImmutable $value): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::Before, $value);
    }

    public function after(DateTimeImmutable $value): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::After, $value);
    }

    public function onOrBefore(DateTimeImmutable $value): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::OnOrBefore, $value);
    }

    public function isEmpty(): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::IsEmpty, true);
    }

    public function isNotEmpty(): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::IsNotEmpty, true);
    }

    public function onOrAfter(DateTimeImmutable $value): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::OnOrAfter, $value);
    }

    public function pastWeek(): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::PastWeek, new stdClass());
    }

    public function pastMonth(): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::PastMonth, new stdClass());
    }

    public function pastYear(): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::PastYear, new stdClass());
    }

    public function nextWeek(): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::NextWeek, new stdClass());
    }

    public function nextMonth(): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::NextMonth, new stdClass());
    }

    public function nextYear(): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::NextYear, new stdClass());
    }

    public function thisWeek(): self
    {
        return new self($this->propertyType, $this->propertyName, Operator::ThisWeek, new stdClass());
    }
}
