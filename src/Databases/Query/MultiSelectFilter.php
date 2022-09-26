<?php

namespace Notion\Databases\Query;

/** @psalm-immutable */
class MultiSelectFilter implements Filter, Condition
{
    private static array $validOperators = [
        Operator::Contains,
        Operator::DoesNotContain,
        Operator::IsEmpty,
        Operator::IsNotEmpty,
    ];

    private function __construct(
        private readonly string $propertyName,
        private readonly Operator $operator,
        private readonly string|bool $value,
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

    public function value(): string|bool
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            $this->propertyType() => $this->propertyName,
            "multi_select" => [
                $this->operator->value => $this->value
            ],
        ];
    }

    public function contains(string $value): self
    {
        return new self($this->propertyName, Operator::Contains, $value);
    }

    public function doesNotContain(string $value): self
    {
        return new self($this->propertyName, Operator::DoesNotContain, $value);
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
