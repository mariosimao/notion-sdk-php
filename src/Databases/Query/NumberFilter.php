<?php

namespace Notion\Databases\Query;

/** @psalm-immutable */
class NumberFilter implements Filter, Condition
{
    private static array $validOperators = [
        Operator::Equals,
        Operator::DoesNotEqual,
        Operator::GreaterThan,
        Operator::LessThan,
        Operator::GreaterThanOrEqualTo,
        Operator::LessThanOrEqualTo,
        Operator::IsEmpty,
        Operator::IsNotEmpty,
    ];


    private function __construct(
        private readonly string $propertyName,
        private readonly Operator $operator,
        private readonly int|float|bool $value,
    ) {
        if (!in_array($operator, self::$validOperators)) {
            throw new \Exception("Invalid operator");
        }
    }

    public static function property(string $propertyName): self
    {
        return new self(
            $propertyName,
            Operator::IsNotEmpty,
            true
        );
    }

    public function propertyType(): string
    {
        return "property";
    }

    public function propertyName(): string
    {
        return $this->propertyName;
    }

    public function operator(): Operator
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
                $this->operator->value => $this->value
            ],
        ];
    }

    public function equals(int|float $value): self
    {
        return new self($this->propertyName, Operator::Equals, $value);
    }

    public function doesNotEqual(int|float $value): self
    {
        return new self($this->propertyName, Operator::DoesNotEqual, $value);
    }

    public function greaterThan(int|float $value): self
    {
        return new self($this->propertyName, Operator::GreaterThan, $value);
    }

    public function lessThan(int|float $value): self
    {
        return new self($this->propertyName, Operator::LessThan, $value);
    }

    public function greaterThanOrEqualTo(int|float $value): self
    {
        return new self($this->propertyName, Operator::GreaterThanOrEqualTo, $value);
    }

    public function lessThanOrEqualTo(int|float $value): self
    {
        return new self($this->propertyName, Operator::LessThanOrEqualTo, $value);
    }

    public function isEmpty(): self
    {
        return new self($this->propertyName, Operator::IsEmpty, true);
    }

    public function isNotEmpty(): self
    {
        return new self($this->propertyName, Operator::IsNotEmpty, true);
    }
}
