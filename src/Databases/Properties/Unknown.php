<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type PropertyJson = array{
 *      id: string,
 *      name: string,
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

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var PropertyJson $array */
        $metdata = PropertyMetadata::fromArray($array);

        return new self($metdata, $array);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
