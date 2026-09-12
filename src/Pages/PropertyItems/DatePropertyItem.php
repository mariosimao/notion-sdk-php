<?php

namespace Notion\Pages\PropertyItems;

use Notion\Common\Date as CommonDate;
use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type DateItemJson = array{
 *      id: string,
 *      type: "date",
 *      date: array{
 *          start: string,
 *          end?: string|null,
 *      }|null,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class DatePropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public CommonDate|null $date,
    ) {
    }

    public static function create(CommonDate|null $date, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Date);

        return new self($metadata, $date);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var DateItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $date = $array["date"] !== null ? CommonDate::fromArray($array["date"]) : null;

        return new self($metadata, $date);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["date"] = $this->date?->toArray();

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return $this->date === null;
    }
}
