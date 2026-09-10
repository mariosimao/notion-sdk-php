<?php

namespace Notion\DataSources\Query;

/** @psalm-immutable */
final readonly class SelectFilter implements Filter, Condition
{
    private const VALID_OPERATORS = [
        Operator::Equals,
        Operator::DoesNotEqual,
        Operator::IsEmpty,
        Operator::IsNotEmpty,
    ];

    private function __construct(
        private string $propertyName,
        private Operator $operator,
        private string|bool $value,
    ) {
        if (!in_array($operator, self::VALID_OPERATORS)) {
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

    /** @return "property" */
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

    public function value(): string|bool
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            $this->propertyType() => $this->propertyName,
            "select"   => [
                $this->operator->value => $this->value
            ],
        ];
    }

    public function equals(string $value): self
    {
        return new self($this->propertyName, Operator::Equals, $value);
    }

    public function doesNotEqual(string $value): self
    {
        return new self($this->propertyName, Operator::DoesNotEqual, $value);
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
