<?php

namespace Notion\Databases\Query;

/** @psalm-immutable */
class FileFilter implements Filter, Condition
{
    private const OPERATOR_IS_EMPTY = "is_empty";
    private const OPERATOR_IS_NOT_EMPTY = "is_not_empty";

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
            self::OPERATOR_IS_NOT_EMPTY,
            true
        );
    }

    public static function createdBy(): self
    {
        return new self(
            "created_by",
            self::OPERATOR_IS_NOT_EMPTY,
            true
        );
    }

    public static function lastEditedBy(): self
    {
        return new self(
            "last_edited_by",
            self::OPERATOR_IS_NOT_EMPTY,
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
            $this->propertyType => $this->propertyName,
            "files" => [
                $this->operator => $this->value
            ],
        ];
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
