<?php

namespace Notion\Databases\Query;

/** @psalm-immutable */
class CheckboxFilter implements Filter, Condition
{
    private const OPERATOR_EQUALS = "equals";
    private const OPERATOR_DOES_NOT_EQUAL = "does_not_equal";

    private string $propertyType = "property";
    private string $propertyName;
    /** @var self::OPERATOR_* */
    private string $operator;
    private bool $value;

    /** @param self::OPERATOR_* $operator */
    private function __construct(
        string $propertyName,
        string $operator,
        bool $value,
    ) {
        $this->propertyName = $propertyName;
        $this->operator = $operator;
        $this->value = $value;
    }

    public static function property(string $propertyName): self
    {
        return new self(
            $propertyName,
            self::OPERATOR_EQUALS,
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

    public function value(): bool
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            $this->propertyType() => $this->propertyName,
            "checkbox" => [
                $this->operator => $this->value
            ],
        ];
    }

    public function equals(bool $value): self
    {
        return new self($this->propertyName, self::OPERATOR_EQUALS, $value);
    }

    public function doesNotEqual(bool $value): self
    {
        return new self($this->propertyName, self::OPERATOR_DOES_NOT_EQUAL, $value);
    }
}
