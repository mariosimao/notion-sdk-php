<?php

namespace Notion\Pages\Properties;

use Notion\Common\Date;
use Notion\DataSources\Properties\RollupFunction;
use stdClass;

/**
 * @psalm-type RollupJson = array{
 *      id: string,
 *      type: "rollup",
 *      rollup: array{
 *          type: "number"|"date"|"array"|"incomplete"|"unsupported",
 *          function: string,
 *          number?: int|float|null,
 *          date?: array{
 *              start: string,
 *              end?: string|null,
 *              time_zone?: string|null
 *          }|null,
 *          array?: list<mixed>,
 *          incomplete?: array<empty, empty>|stdClass,
 *          unsupported?: array<empty, empty>|stdClass
 *      }
 * }
 *
 * @psalm-immutable
 */
final readonly class Rollup implements PropertyInterface
{
    /**
     * @param list<mixed> $array Raw property values aggregated by the rollup.
     */
    private function __construct(
        private PropertyMetadata $metadata,
        public RollupType $type,
        public RollupFunction $function,
        public int|float|null $number = null,
        public Date|null $date = null,
        public array $array = [],
    ) {
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RollupJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        $rollup = $array["rollup"];
        $type = RollupType::from($rollup["type"]);
        $function = RollupFunction::from($rollup["function"]);

        $number = $type === RollupType::Number ? ($rollup["number"] ?? null) : null;

        $date = null;
        if ($type === RollupType::Date && isset($rollup["date"])) {
            $date = Date::fromArray($rollup["date"]);
        }

        $values = $type === RollupType::Array ? ($rollup["array"] ?? []) : [];

        return new self($metadata, $type, $function, $number, $date, $values);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $rollup = [
            "type" => $this->type->value,
            "function" => $this->function->value,
        ];

        switch ($this->type) {
            case RollupType::Number:
                $rollup["number"] = $this->number;
                break;
            case RollupType::Date:
                $rollup["date"] = $this->date?->toArray();
                break;
            case RollupType::Array:
                $rollup["array"] = $this->array;
                break;
            case RollupType::Incomplete:
                $rollup["incomplete"] = new stdClass();
                break;
            case RollupType::Unsupported:
                $rollup["unsupported"] = new stdClass();
                break;
        }

        $array["rollup"] = $rollup;

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function isNumber(): bool
    {
        return $this->type === RollupType::Number;
    }

    public function isDate(): bool
    {
        return $this->type === RollupType::Date;
    }

    public function isArray(): bool
    {
        return $this->type === RollupType::Array;
    }

    public function isIncomplete(): bool
    {
        return $this->type === RollupType::Incomplete;
    }

    /**
     * The value is unavailable because it depends on too many related pages
     * or nested formulas and rollups. Only the rollup function is available.
     */
    public function isUnsupported(): bool
    {
        return $this->type === RollupType::Unsupported;
    }
}
