<?php

namespace Notion\DataSources\Query;

/** @psalm-immutable */
final readonly class PeopleFilter implements Filter, Condition
{
    private const VALID_OPERATORS = [
        Operator::Contains,
        Operator::DoesNotContain,
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

    public static function createdBy(): self
    {
        return new self(
            "created_by",
            Operator::IsNotEmpty,
            true
        );
    }

    public static function lastEditedBy(): self
    {
        return new self(
            "last_edited_by",
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
            "people" => [
                $this->operator->value => $this->value
            ],
        ];
    }

    public function contains(string $userId): self
    {
        return new self($this->propertyName, Operator::Contains, $userId);
    }

    public function doesNotContain(string $userId): self
    {
        return new self($this->propertyName, Operator::DoesNotContain, $userId);
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
