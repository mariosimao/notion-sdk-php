<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type UrlJson = array{
 *      id: string,
 *      name: string,
 *      type: "url",
 *      url: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class Url implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "Url"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::Url);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var UrlJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["url"] = new \stdClass();

        return $array;
    }
}
