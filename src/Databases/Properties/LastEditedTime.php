<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type LastEditedTimeJson = array{
 *      id: string,
 *      name: string,
 *      type: "last_edited_time",
 *      last_edited_time: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class LastEditedTime implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "LastEditedTime"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::LastEditedTime);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var LastEditedTimeJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["last_edited_time"] = new \stdClass();

        return $array;
    }
}
