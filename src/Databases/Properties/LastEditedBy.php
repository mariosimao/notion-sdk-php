<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type LastEditedByJson = array{
 *      id: string,
 *      name: string,
 *      type: "last_edited_by",
 *      last_edited_by: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class LastEditedBy implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "LastEditedBy"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::LastEditedBy);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var LastEditedByJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["last_edited_by"] = new \stdClass();

        return $array;
    }
}
