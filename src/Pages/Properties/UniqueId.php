<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type UniqueIdJson = array{
 *      id: string,
 *      type: "unique_id",
 *      unique_id: array{
 *          number: int,
 *          prefix: string|null,
 *      },
 * }
 *
 * @psalm-immutable
 */
class UniqueId implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly int $number,
        public readonly string|null $prefix,
    ) {
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var UniqueIdJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        $number = $array["unique_id"]["number"];
        $prefix = $array["unique_id"]["prefix"];

        return new self($metadata, $number, $prefix);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["unique_id"] = [
            "number" => $this->number,
            "prefix" => $this->prefix,
        ];

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }
}
