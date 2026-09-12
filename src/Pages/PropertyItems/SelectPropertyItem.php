<?php

namespace Notion\Pages\PropertyItems;

use Notion\DataSources\Properties\SelectOption;
use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type SelectItemJson = array{
 *      id: string,
 *      type: "select",
 *      select: array{ id: string, name: string, color: string }|null,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class SelectPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public SelectOption|null $option,
    ) {
    }

    public static function create(SelectOption|null $option, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Select);

        return new self($metadata, $option);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var SelectItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $option = $array["select"] ? SelectOption::fromArray($array["select"]) : null;

        return new self($metadata, $option);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["select"] = $this->option?->toArray();

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return $this->option === null;
    }
}
