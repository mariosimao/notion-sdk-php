<?php

namespace Notion\Pages\PropertyItems;

use Notion\Common\Date;
use Notion\Pages\Properties\FormulaType;
use Notion\Pages\Properties\PropertyType;
use stdClass;

/**
 * @psalm-type FormulaItemJson = array{
 *      id: string,
 *      type: "formula",
 *      formula: array{
 *          type: string,
 *          string?: string|null,
 *          number?: int|float|null,
 *          boolean?: bool|null,
 *          date?: array{
 *              start: string,
 *              end?: string|null,
 *              time_zone?: string|null,
 *          }|null,
 *          unsupported?: array<empty, empty>|stdClass,
 *      },
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class FormulaPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public FormulaType $formulaType,
        public string|null $string = null,
        public int|float|null $number = null,
        public bool|null $boolean = null,
        public Date|null $date = null,
        public bool $unsupported = false,
    ) {
    }

    public static function createString(string $string, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Formula);

        return new self($metadata, FormulaType::String, string: $string);
    }

    public static function createNumber(int|float $number, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Formula);

        return new self($metadata, FormulaType::Number, number: $number);
    }

    public static function createBoolean(bool $boolean, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Formula);

        return new self($metadata, FormulaType::Boolean, boolean: $boolean);
    }

    public static function createDate(Date $date, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Formula);

        return new self($metadata, FormulaType::Date, date: $date);
    }

    public static function createUnsupported(string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Formula);

        return new self($metadata, FormulaType::Unsupported, unsupported: true);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var FormulaItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);

        /** @var array<string, mixed> $formula */
        $formula = $array["formula"] ?? [];
        $typeString = isset($formula["type"]) && is_string($formula["type"]) ? $formula["type"] : "unsupported";
        $type = FormulaType::tryFrom($typeString) ?? FormulaType::Unsupported;

        $string = isset($formula["string"]) && is_string($formula["string"]) ? $formula["string"] : null;
        $number = isset($formula["number"]) && (is_int($formula["number"]) || is_float($formula["number"]))
            ? $formula["number"]
            : null;
        $boolean = isset($formula["boolean"]) && is_bool($formula["boolean"]) ? $formula["boolean"] : null;

        /** @psalm-var array{start: string, end?: string|null}|null $dateArray */
        $dateArray = isset($formula["date"]) && is_array($formula["date"]) ? $formula["date"] : null;
        $date = $dateArray !== null ? Date::fromArray($dateArray) : null;
        $unsupported = $type === FormulaType::Unsupported;

        return new self($metadata, $type, $string, $number, $boolean, $date, $unsupported);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        /** @var array<string, mixed> $formula */
        $formula = ["type" => $this->formulaType->value];

        if ($this->formulaType === FormulaType::String) {
            $formula["string"] = $this->string;
        } elseif ($this->formulaType === FormulaType::Number) {
            $formula["number"] = $this->number;
        } elseif ($this->formulaType === FormulaType::Boolean) {
            $formula["boolean"] = $this->boolean;
        } elseif ($this->formulaType === FormulaType::Date) {
            $formula["date"] = $this->date?->toArray();
        } else {
            $formula["unsupported"] = new stdClass();
        }

        $array["formula"] = $formula;

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isString(): bool
    {
        return $this->formulaType === FormulaType::String;
    }

    public function isNumber(): bool
    {
        return $this->formulaType === FormulaType::Number;
    }

    public function isBoolean(): bool
    {
        return $this->formulaType === FormulaType::Boolean;
    }

    public function isDate(): bool
    {
        return $this->formulaType === FormulaType::Date;
    }

    public function isUnsupported(): bool
    {
        return $this->formulaType === FormulaType::Unsupported;
    }
}
