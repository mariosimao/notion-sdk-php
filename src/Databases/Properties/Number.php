<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type NumberJson = array{
 *      id: string,
 *      name: string,
 *      type: "number",
 *      number: array{ format: string },
 * }
 *
 * @psalm-immutable
 */
class Number implements PropertyInterface
{
    private const TYPE = Property::TYPE_NUMBER;

    public const FORMAT_NUMBER = "number";
    public const FORMAT_NUMBER_WITH_COMMAS = "number_with_commas";
    public const FORMAT_PERCENT = "percent";
    public const FORMAT_DOLLAR = "dollar";
    public const FORMAT_CANADIAN_DOLLAR = "canadian_dollar";
    public const FORMAT_EURO = "euro";
    public const FORMAT_POUND = "pound";
    public const FORMAT_YEN = "yen";
    public const FORMAT_RUBLE = "ruble";
    public const FORMAT_RUPEE = "rupee";
    public const FORMAT_WON = "won";
    public const FORMAT_YUAN = "yuan";
    public const FORMAT_REAL = "real";
    public const FORMAT_LIRA = "lira";
    public const FORMAT_RUPIAH = "rupiah";
    public const FORMAT_FRANC = "franc";
    public const FORMAT_HONG_KONG_DOLLAR = "hong_kong_dollar";
    public const FORMAT_NEW_ZEALAND_DOLLAR = "new_zealand_dollar";
    public const FORMAT_KRONA = "krona";
    public const FORMAT_NORWEGIAN_KRONE = "norwegian_krone";
    public const FORMAT_MEXICAN_PESO = "mexican_peso";
    public const FORMAT_RAND = "rand";
    public const FORMAT_NEW_TAIWAN_DOLLAR = "new_taiwan_dollar";
    public const FORMAT_DANISH_KRONE = "danish_krone";
    public const FORMAT_ZLOTY = "zloty";
    public const FORMAT_BAHT = "baht";
    public const FORMAT_FORINT = "forint";
    public const FORMAT_KORUNA = "koruna";
    public const FORMAT_SHEKEL = "shekel";
    public const FORMAT_CHILEAN_PESO = "chilean_peso";
    public const FORMAT_PHILIPPINE_PESO = "philippine_peso";
    public const FORMAT_DIRHAM = "dirham";
    public const FORMAT_COLOMBIAN_PESO = "colombian_peso";
    public const FORMAT_RIYAL = "riyal";
    public const FORMAT_RINGGIT = "ringgit";
    public const FORMAT_LEU = "leu";

    private Property $property;
    private string $format;

    private function __construct(Property $property, string $format)
    {
        $this->property = $property;
        $this->format = $format;
    }

    public static function create(string $propertyName = "Number", string $format = "number"): self
    {
        $property = Property::create("", $propertyName, self::TYPE);

        return new self($property, $format);
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function format(): string
    {
        return $this->format;
    }

    public function withFormat(string $format): self
    {
        return new self($this->property, $format);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var NumberJson $array */
        $property = Property::fromArray($array);
        $format = $array[self::TYPE]["format"];

        return new self($property, $format);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();
        $array[self::TYPE] = [
            "format" => $this->format,
        ];

        return $array;
    }
}
