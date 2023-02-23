<?php

namespace Notion\Pages\Properties;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * @psalm-type FormulaJson = array{
 *      id: string,
 *      type: "formula",
 *      formula: array{
 *          type: "string"|"number"|"boolean"|"date",
 *          string?: string,
 *          number?: int|float,
 *          'boolean'?: bool,
 *          date?: array{
 *              start: string,
 *              end: string|null
 *          }
 *      }
 * }
 *
 * @psalm-immutable
 */
class Formula implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly FormulaType $type,
        public readonly string|null $string,
        public readonly int|float|null $number,
        public readonly bool|null $boolean,
        public readonly Date|null $date,
    ) {
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var FormulaJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        $formula = $array["formula"];
        $type = FormulaType::from($formula["type"]);

        $string = $formula["string"] ?? null;
        $number = $formula["number"] ?? null;
        $boolean = isset($formula["boolean"]) ? $formula["boolean"] : null;

        $date = null;
        if (isset($formula["date"])) {
            $date = Date::fromArray($formula["date"]);
        }

        return new self($metadata, $type, $string, $number, $boolean, $date);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["formula"] = [ "type" => $this->type->value ];

        switch ($this->type) {
            case FormulaType::String:
                $array["formula"]["string"] = $this->string;
                break;
            case FormulaType::Number:
                $array["formula"]["number"] = $this->number;
                break;
            case FormulaType::Boolean:
                $array["formula"]["boolean"] = $this->boolean;
                break;
            case FormulaType::Date:
                $array["formula"]["date"] = $this->date?->toArray();
                break;
        }

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }
}
