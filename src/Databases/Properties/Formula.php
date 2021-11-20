<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type FormulaJson = array{
 *      id: string,
 *      name: string,
 *      type: "formula",
 *      formula: array{ expression: string },
 * }
 *
 * @psalm-immutable
 */
class Formula implements PropertyInterface
{
    private const TYPE = Property::TYPE_FORMULA;

    private Property $property;
    private string $expression;

    private function __construct(Property $property, string $expression)
    {
        $this->property = $property;
        $this->expression = $expression;
    }

    public static function create(string $propertyName = "Formula", string $expression = ""): self
    {
        $property = Property::create("", $propertyName, self::TYPE);

        return new self($property, $expression);
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function expression(): string
    {
        return $this->expression;
    }

    public function withExpression(string $expression): self
    {
        return new self($this->property, $expression);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var FormulaJson $array */
        $property = Property::fromArray($array);
        $expression = $array[self::TYPE]["expression"];

        return new self($property, $expression);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();
        $array[self::TYPE] = [
            "expression" => $this->expression,
        ];

        return $array;
    }
}
