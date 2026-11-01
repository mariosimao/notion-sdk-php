<?php

namespace Notion\Pages\Properties;

use Notion\Common\Date;
use stdClass;

/**
 * @psalm-type FormulaJson = array{
 *      id: string,
 *      type: "formula",
 *      formula: array{
 *          type: "string"|"number"|"boolean"|"date"|"unsupported",
 *          string?: string|null,
 *          number?: int|float|null,
 *          'boolean'?: bool|null,
 *          date?: array{
 *              start: string,
 *              end?: string|null,
 *              time_zone?: string|null
 *          }|null,
 *          unsupported?: array<empty, empty>|stdClass
 *      }
 * }
 *
 * @psalm-immutable
 */
final readonly class Formula implements PropertyInterface
{
    private function __construct(
        private PropertyMetadata $metadata,
        public FormulaType $type,
        public string|null $string,
        public int|float|null $number,
        public bool|null $boolean,
        public Date|null $date,
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
            case FormulaType::Unsupported:
                $array["formula"]["unsupported"] = new stdClass();
                break;
        }

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function isString(): bool
    {
        return $this->type === FormulaType::String;
    }

    public function isNumber(): bool
    {
        return $this->type === FormulaType::Number;
    }

    public function isBoolean(): bool
    {
        return $this->type === FormulaType::Boolean;
    }

    public function isDate(): bool
    {
        return $this->type === FormulaType::Date;
    }

    /**
     * The value is unavailable because it depends on too many related pages
     * or nested formulas and rollups. No partial value is exposed.
     */
    public function isUnsupported(): bool
    {
        return $this->type === FormulaType::Unsupported;
    }
}
