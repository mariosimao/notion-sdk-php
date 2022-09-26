<?php

namespace Notion\Pages\Properties;

use Notion\Databases\Properties\SelectOption;

/**
 * @psalm-import-type SelectOptionJson from SelectOption
 *
 * @psalm-type MultiSelectJson = array{
 *      id: string,
 *      type: "multi_select",
 *      multi_select: SelectOptionJson[],
 * }
 *
 * @psalm-immutable
 */
class MultiSelect implements PropertyInterface
{
    /** @param SelectOption[] $options */
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly array $options
    ) {
    }

    public static function fromIds(string ...$ids): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::MultiSelect);
        $options = array_map(fn(string $id) => SelectOption::fromId($id), $ids);

        return new self($metadata, $options);
    }

    public static function fromNames(string ...$names): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::MultiSelect);
        $options = array_map(fn(string $name) => SelectOption::fromName($name), $names);

        return new self($metadata, $options);
    }

    public static function fromOptions(SelectOption ...$options): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::MultiSelect);

        return new self($metadata, $options);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var MultiSelectJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        $options = array_map(fn(array $option) => SelectOption::fromArray($option), $array["multi_select"]);

        return new self($metadata, $options);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["multi_select"] = array_map(fn (SelectOption $option) => $option->toArray(), $this->options);

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function addOption(SelectOption $option): self
    {
        $options = $this->options;
        $options[] = $option;

        return new self($this->metadata, $options);
    }

    public function removeOption(string $optionId): self
    {
        return new self(
            $this->metadata,
            array_filter($this->options, fn (SelectOption $o) => $o->id !== $optionId),
        );
    }
}
