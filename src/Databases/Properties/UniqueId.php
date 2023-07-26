<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type UniqueIdJson = array{
 *      id: string,
 *      name: string,
 *      type: "uniqueId",
 *      unique_id: \stdClass,
 * }
 *
 * @psalm-immutable
 */
class UniqueId implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var UniqueIdJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["unique_id"] = new \stdClass();

        return $array;
    }
}
