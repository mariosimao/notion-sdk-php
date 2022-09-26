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
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly string $expression
    ) {
    }

    public static function create(string $propertyName = "Formula", string $expression = ""): self
    {
        $property = PropertyMetadata::create("", $propertyName, PropertyType::Formula);

        return new self($property, $expression);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeExpression(string $expression): self
    {
        return new self($this->metadata, $expression);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var FormulaJson $array */
        $property = PropertyMetadata::fromArray($array);
        $expression = $array["formula"]["expression"];

        return new self($property, $expression);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["formula"] = [
            "expression" => $this->expression,
        ];

        return $array;
    }
}
