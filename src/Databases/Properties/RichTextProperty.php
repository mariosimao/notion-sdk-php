<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type RichTextJson = array{
 *      id: string,
 *      name: string,
 *      type: "rich_text",
 *      rich_text: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class RichTextProperty implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "Text"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::RichText);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RichTextJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["rich_text"] = new \stdClass();

        return $array;
    }
}
