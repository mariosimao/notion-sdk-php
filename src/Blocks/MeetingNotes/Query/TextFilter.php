<?php

namespace Notion\Blocks\MeetingNotes\Query;

/** @psalm-immutable */
final readonly class TextFilter implements PropertyFilter
{
    private const VALID_OPERATORS = [
        Operator::StringIs,
        Operator::StringIsNot,
        Operator::StringContains,
        Operator::StringDoesNotContain,
        Operator::StringStartsWith,
        Operator::StringEndsWith,
        Operator::IsEmpty,
        Operator::IsNotEmpty,
    ];

    private function __construct(
        private Property $property,
        private Operator $operator,
        private string|null $value,
    ) {
        if (!in_array($operator, self::VALID_OPERATORS, true)) {
            throw new \InvalidArgumentException("Invalid operator");
        }
    }

    public static function title(): self
    {
        return new self(Property::Title, Operator::IsNotEmpty, null);
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function operator(): Operator
    {
        return $this->operator;
    }

    public function value(): string|null
    {
        return $this->value;
    }

    public function equals(string $value): self
    {
        return new self($this->property, Operator::StringIs, $value);
    }

    public function doesNotEqual(string $value): self
    {
        return new self($this->property, Operator::StringIsNot, $value);
    }

    public function contains(string $value): self
    {
        return new self($this->property, Operator::StringContains, $value);
    }

    public function doesNotContain(string $value): self
    {
        return new self($this->property, Operator::StringDoesNotContain, $value);
    }

    public function startsWith(string $value): self
    {
        return new self($this->property, Operator::StringStartsWith, $value);
    }

    public function endsWith(string $value): self
    {
        return new self($this->property, Operator::StringEndsWith, $value);
    }

    public function isEmpty(): self
    {
        return new self($this->property, Operator::IsEmpty, null);
    }

    public function isNotEmpty(): self
    {
        return new self($this->property, Operator::IsNotEmpty, null);
    }

    public function toArray(): array
    {
        $filter = [ "operator" => $this->operator->value ];

        if ($this->value !== null) {
            $filter["value"] = [
                "type"  => "exact",
                "value" => $this->value,
            ];
        }

        return [
            "property" => $this->property->value,
            "filter"   => $filter,
        ];
    }
}
