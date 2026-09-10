<?php

namespace Notion\DataSources\Properties;

/**
 * @psalm-type PropertyJson = array{
 *      id: string,
 *      name: string,
 *      type: string,
 *      description?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Unknown implements PropertyInterface
{
    private function __construct(
        private PropertyMetadata $metadata,
        private array $data,
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
