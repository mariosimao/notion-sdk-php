<?php

namespace Notion\DataSources\Query;

/** @psalm-immutable */
final readonly class StatusFilter implements Filter, Condition
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
        /** @var string|non-empty-list<string>|bool */
        private string|array|bool $value,
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

    /** @return string|non-empty-list<string>|bool */
    public function value(): string|array|bool
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            $this->propertyType() => $this->propertyName,
            "status"   => [
                $this->operator->value => $this->value
            ],
        ];
    }

    /**
     * Accepts a single value or a list of values. Entries matching any of the
     * provided values are considered a match.
     *
     * @param string|list<string> $value
     */
    public function equals(string|array $value): self
    {
        return new self($this->propertyName, Operator::Equals, self::validateValue($value));
    }

    /**
     * Accepts a single value or a list of values. Entries matching none of the
     * provided values are considered a match.
     *
     * @param string|list<string> $value
     */
    public function doesNotEqual(string|array $value): self
    {
        return new self($this->propertyName, Operator::DoesNotEqual, self::validateValue($value));
    }

    public function isEmpty(): self
    {
        return new self($this->propertyName, Operator::IsEmpty, true);
    }

    public function isNotEmpty(): self
    {
        return new self($this->propertyName, Operator::IsNotEmpty, true);
    }

    /**
     * @param string|list<string> $value
     *
     * @return string|non-empty-list<string>
     *
     * @psalm-pure
     */
    private static function validateValue(string|array $value): string|array
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value === []) {
            throw new \InvalidArgumentException("At least one value must be provided.");
        }

        /** @psalm-suppress RedundantCondition */
        if (!array_is_list($value)) {
            throw new \InvalidArgumentException("Values must be a list.");
        }

        foreach ($value as $item) {
            /** @psalm-suppress DocblockTypeContradiction */
            if (!is_string($item)) {
                throw new \InvalidArgumentException("All values must be strings.");
            }
        }

        return $value;
    }
}
