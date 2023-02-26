<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type UnknownJson = array{
 *      id: string,
 *      type: string
 * }
 *
 * @psalm-immutable
 */
class Unknown implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        private readonly array $data,
    ) {
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var UnknownJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata, $array);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }
}
