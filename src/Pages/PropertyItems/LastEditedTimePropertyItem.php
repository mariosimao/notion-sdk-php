<?php

namespace Notion\Pages\PropertyItems;

use DateTimeImmutable;
use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type LastEditedTimeItemJson = array{
 *      id: string,
 *      type: "last_edited_time",
 *      last_edited_time: string,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class LastEditedTimePropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public DateTimeImmutable $lastEditedTime,
    ) {
    }

    public static function create(DateTimeImmutable $lastEditedTime, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::LastEditedTime);

        return new self($metadata, $lastEditedTime);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var LastEditedTimeItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $lastEditedTime = new DateTimeImmutable($array["last_edited_time"]);

        return new self($metadata, $lastEditedTime);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["last_edited_time"] = $this->lastEditedTime->format("Y-m-d\TH:i:s.uP");

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }
}
