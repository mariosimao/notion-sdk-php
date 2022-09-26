<?php

namespace Notion\Databases\Query;

/** @psalm-immutable */
class CheckboxFilter implements Filter, Condition
{
    private static array $validOperators = [
        Operator::Equals,
        Operator::DoesNotEqual,
    ];

    private function __construct(
        private readonly string $propertyName,
        private readonly Operator $operator,
        private readonly bool $value,
    ) {
        if (!in_array($operator, self::$validOperators)) {
            throw new \Exception("Invalid operator");
        }
    }

    public static function property(string $propertyName): self
    {
        return new self($propertyName, Operator::Equals, true);
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

    public function value(): bool
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            $this->propertyType() => $this->propertyName,
            "checkbox" => [
                $this->operator->value => $this->value
            ],
        ];
    }

    public function equals(bool $value): self
    {
        return new self($this->propertyName, Operator::Equals, $value);
    }

    public function doesNotEqual(bool $value): self
    {
        return new self($this->propertyName, Operator::DoesNotEqual, $value);
    }
}
