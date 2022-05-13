<?php

namespace Notion\Databases\Query;

/** @psalm-immutable */
class NumberFilter implements Filter, Condition
{
    private const OPERATOR_EQUALS = "equals";
    private const OPERATOR_DOES_NOT_EQUAL = "does_not_equal";
    private const OPERATOR_GREATER_THAN = "greater_than";
    private const OPERATOR_LESS_THAN = "less_than";
    private const OPERATOR_GREATER_THAN_OR_EQUAL_TO = "greater_than_or_equal_to";
    private const OPERATOR_LESS_THAN_OR_EQUAL_TO = "less_than_or_equal_to";
    private const OPERATOR_IS_EMPTY = "is_empty";
    private const OPERATOR_IS_NOT_EMPTY = "is_not_empty";

    /** @var "property" */
    private string $propertyType = "property";
    private string $propertyName;
    /** @var self::OPERATOR_* */
    private string $operator;
    private int|float|bool $value;

    /** @param self::OPERATOR_* $operator */
    private function __construct(
        string $propertyName,
        string $operator,
        int|float|bool $value,
    ) {
        $this->propertyName = $propertyName;
        $this->operator = $operator;
        $this->value = $value;
    }

    public static function property(string $propertyName): self
    {
        return new self(
            $propertyName,
            self::OPERATOR_IS_NOT_EMPTY,
            true
        );
    }

    /** @return "property" */
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

    public function value(): int|float|bool
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            $this->propertyType() => $this->propertyName,
            "number"   => [
                $this->operator => $this->value
            ],
        ];
    }

    public function equals(int|float $value): self
    {
        return new self($this->propertyName, self::OPERATOR_EQUALS, $value);
    }

    public function doesNotEqual(int|float $value): self
    {
        return new self($this->propertyName, self::OPERATOR_DOES_NOT_EQUAL, $value);
    }

    public function greaterThan(int|float $value): self
    {
        return new self($this->propertyName, self::OPERATOR_GREATER_THAN, $value);
    }

    public function lessThan(int|float $value): self
    {
        return new self($this->propertyName, self::OPERATOR_LESS_THAN, $value);
    }

    public function greaterThanOrEqualTo(int|float $value): self
    {
        return new self($this->propertyName, self::OPERATOR_GREATER_THAN_OR_EQUAL_TO, $value);
    }

    public function lessThanOrEqualTo(int|float $value): self
    {
        return new self($this->propertyName, self::OPERATOR_LESS_THAN_OR_EQUAL_TO, $value);
    }

    public function isEmpty(): self
    {
        return new self($this->propertyName, self::OPERATOR_IS_EMPTY, true);
    }

    public function isNotEmpty(): self
    {
        return new self($this->propertyName, self::OPERATOR_IS_NOT_EMPTY, true);
    }
}
