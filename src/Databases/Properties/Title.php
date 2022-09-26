<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type TitleJson = array{
 *      id: "title",
 *      name: string,
 *      type: "title",
 *      title: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class Title implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "Title"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::Title);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var TitleJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["title"] = new \stdClass();

        return $array;
    }
}
