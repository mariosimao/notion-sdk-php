<?php

namespace Notion\Pages\PropertyItems;

use Notion\DataSources\Properties\SelectOption;
use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-import-type SelectOptionJson from \Notion\DataSources\Properties\SelectOption
 *
 * @psalm-type MultiSelectItemJson = array{
 *      id: string,
 *      type: "multi_select",
 *      multi_select: SelectOptionJson[],
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class MultiSelectPropertyItem implements PropertyItemInterface
{
    /** @param list<SelectOption> $options */
    private function __construct(
        private PropertyItemMetadata $metadata,
        public array $options,
    ) {
    }

    /** @param list<SelectOption> $options */
    public static function create(array $options, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::MultiSelect);

        return new self($metadata, $options);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var MultiSelectItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        /** @var list<SelectOption> $options */
        $options = array_map(
            fn(array $option) => SelectOption::fromArray($option),
            $array["multi_select"] ?? [],
        );

        return new self($metadata, $options);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["multi_select"] = array_map(fn(SelectOption $o) => $o->toArray(), $this->options);

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return empty($this->options);
    }
}
