<?php

namespace Notion\Pages\PropertyItems;

use DateTimeImmutable;
use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type CreatedTimeItemJson = array{
 *      id: string,
 *      type: "created_time",
 *      created_time: string,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class CreatedTimePropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public DateTimeImmutable $createdTime,
    ) {
    }

    public static function create(DateTimeImmutable $createdTime, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::CreatedTime);

        return new self($metadata, $createdTime);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CreatedTimeItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $createdTime = new DateTimeImmutable($array["created_time"]);

        return new self($metadata, $createdTime);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["created_time"] = $this->createdTime->format("Y-m-d\TH:i:s.uP");

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }
}
