<?php

namespace Notion\Pages\PropertyItems;

use Notion\Common\Date;
use Notion\DataSources\Properties\RollupFunction;
use Notion\Pages\Properties\PropertyType;
use stdClass;

/**
 * @psalm-type RollupItemJson = array{
 *      id: string,
 *      type: "rollup",
 *      rollup: array{
 *          type: string,
 *          function: string,
 *          number?: int|float|null,
 *          date?: array{
 *              start: string,
 *              end?: string|null,
 *              time_zone?: string|null,
 *          }|null,
 *          array?: list<mixed>,
 *          incomplete?: array<empty, empty>|stdClass,
 *          unsupported?: array<empty, empty>|stdClass,
 *      },
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class RollupPropertyItem implements PropertyItemInterface
{
    /**
     * @param list<mixed> $array
     */
    private function __construct(
        private PropertyItemMetadata $metadata,
        public RollupType $rollupType,
        public RollupFunction $function,
        public int|float|null $number = null,
        public Date|null $date = null,
        public array $array = [],
        public bool $incomplete = false,
        public bool $unsupported = false,
    ) {
    }

    public static function createNumber(
        RollupFunction $function,
        int|float|null $number,
        string $id = "",
    ): self {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Rollup);

        return new self($metadata, RollupType::Number, $function, number: $number);
    }

    public static function createDate(
        RollupFunction $function,
        Date|null $date,
        string $id = "",
    ): self {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Rollup);

        return new self($metadata, RollupType::Date, $function, date: $date);
    }

    /**
     * @param list<mixed> $array
     */
    public static function createArray(
        RollupFunction $function,
        array $array,
        string $id = "",
    ): self {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Rollup);

        return new self($metadata, RollupType::Array, $function, array: $array);
    }

    public static function createIncomplete(RollupFunction $function, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Rollup);

        return new self($metadata, RollupType::Incomplete, $function, incomplete: true);
    }

    public static function createUnsupported(RollupFunction $function, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Rollup);

        return new self($metadata, RollupType::Unsupported, $function, unsupported: true);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RollupItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);

        /** @var array{type: string, function: string, number?: int|float|null, date?: array{start: string, end?: string|null}|null, array?: list<mixed>} $data */
        $data = $array["rollup"];

        $rollupType = RollupType::from($data["type"]);
        $function = RollupFunction::from($data["function"]);

        $number = isset($data["number"]) && (is_int($data["number"]) || is_float($data["number"]))
            ? $data["number"]
            : null;

        $date = isset($data["date"])
            ? Date::fromArray($data["date"])
            : null;

        /** @var list<mixed> $arrayValues */
        $arrayValues = $rollupType === RollupType::Array && isset($data["array"])
            ? $data["array"]
            : [];

        $incomplete = $rollupType === RollupType::Incomplete;
        $unsupported = $rollupType === RollupType::Unsupported;

        return new self(
            $metadata,
            $rollupType,
            $function,
            number: $number,
            date: $date,
            array: $arrayValues,
            incomplete: $incomplete,
            unsupported: $unsupported,
        );
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        /** @var array<string, mixed> $rollup */
        $rollup = [
            "type" => $this->rollupType->value,
            "function" => $this->function->value,
        ];

        if ($this->rollupType === RollupType::Number) {
            $rollup["number"] = $this->number;
        } elseif ($this->rollupType === RollupType::Date) {
            $rollup["date"] = $this->date?->toArray();
        } elseif ($this->rollupType === RollupType::Array) {
            $rollup["array"] = $this->array;
        } elseif ($this->rollupType === RollupType::Incomplete) {
            $rollup["incomplete"] = new stdClass();
        } else {
            $rollup["unsupported"] = new stdClass();
        }

        $array["rollup"] = $rollup;

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isNumber(): bool
    {
        return $this->rollupType === RollupType::Number;
    }

    public function isDate(): bool
    {
        return $this->rollupType === RollupType::Date;
    }

    public function isArray(): bool
    {
        return $this->rollupType === RollupType::Array;
    }

    public function isIncomplete(): bool
    {
        return $this->rollupType === RollupType::Incomplete;
    }

    public function isUnsupported(): bool
    {
        return $this->rollupType === RollupType::Unsupported;
    }

    public function isEmpty(): bool
    {
        return match ($this->rollupType) {
            RollupType::Number => $this->number === null,
            RollupType::Date => $this->date === null,
            RollupType::Array => empty($this->array),
            RollupType::Incomplete, RollupType::Unsupported => true,
        };
    }
}
