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
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly NumberFormat $format,
    ) {
    }

    public static function create(
        string $propertyName = "Number",
        NumberFormat $format = NumberFormat::Number,
    ): self {
        $property = PropertyMetadata::create("", $propertyName, PropertyType::Number);

        return new self($property, $format);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeFormat(NumberFormat $format): self
    {
        return new self($this->metadata, $format);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var NumberJson $array */
        $property = PropertyMetadata::fromArray($array);
        $format = NumberFormat::from($array["number"]["format"]);

        return new self($property, $format);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["number"] = [ "format" => $this->format->value ];

        return $array;
    }
}
