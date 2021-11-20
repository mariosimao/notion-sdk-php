<?php

namespace Notion\Pages\Properties;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\RichText;

/**
 * @psalm-type FormulaJson = array{
 *      id: string,
 *      type: "formula",
 *      formula: array{
 *          type: "string"|"number"|"boolean"|"date",
 *          string?: string,
 *          number?: int|float,
 *          boolean?: bool,
 *          date?: array{
 *              start: string,
 *              end: string|null,
 *          },
 *      },
 * }
 *
 * @psalm-immutable
 */
class Formula implements PropertyInterface
{
    private const TYPE = Property::TYPE_FORMULA;

    private Property $property;

    /** @var "string"|"number"|"boolean"|"date" */
    private string $type;
    private string|null $string;
    private int|float|null $number;
    private bool|null $boolean;
    private DateTimeImmutable|null $start;
    private DateTimeImmutable|null $end;

    /**
     * @param "string"|"number"|"boolean"|"date" $type
     */
    private function __construct(
        Property $property,
        string $type,
        string|null $string,
        int|float|null $number,
        bool|null $boolean,
        DateTimeImmutable|null $start,
        DateTimeImmutable|null $end,
    ) {
        $this->property = $property;
        $this->type = $type;
        $this->string = $string;
        $this->number = $number;
        $this->boolean = $boolean;
        $this->start = $start;
        $this->end = $end;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var FormulaJson $array */
        $property = Property::fromArray($array);

        $formula = $array[self::TYPE];
        $type = $formula["type"];

        $string = $formula["string"] ?? null;
        $number = $formula["number"] ?? null;
        $boolean = isset($formula["boolean"]) ? (bool) $formula["boolean"] : null;
        $start = isset($formula["date"]["start"]) ?
            new DateTimeImmutable($formula["date"]["start"]) : null;
        $end = isset($formula["date"]["end"]) ?
            new DateTimeImmutable($formula["date"]["end"]) : null;

        return new self(
            $property,
            $type,
            $string,
            $number,
            $boolean,
            $start,
            $end,
        );
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = [ "type" => $this->type ];

        switch ($this->type) {
            case "string":
                $array[self::TYPE]["string"] = $this->string;
                break;
            case "number":
                $array[self::TYPE]["number"] = $this->number;
                break;
            case "boolean":
                $array[self::TYPE]["boolean"] = $this->boolean;
                break;
            case "date":
                $array[self::TYPE]["date"]["start"] = $this->start?->format(Date::FORMAT);
                $array[self::TYPE]["date"]["end"] = $this->end?->format(Date::FORMAT);
                break;
        }

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function isString(): bool
    {
        return $this->type === "string";
    }

    public function isNumber(): bool
    {
        return $this->type === "number";
    }

    public function isBoolean(): bool
    {
        return $this->type === "boolean";
    }

    public function isDate(): bool
    {
        return $this->type === "date";
    }

    public function string(): string|null
    {
        return $this->string;
    }

    public function number(): int|float|null
    {
        return $this->number;
    }

    public function boolean(): bool|null
    {
        return $this->boolean;
    }

    public function start(): DateTimeImmutable|null
    {
        return $this->start;
    }

    public function end(): DateTimeImmutable|null
    {
        return $this->end;
    }
}
