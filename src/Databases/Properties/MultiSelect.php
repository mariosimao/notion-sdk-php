<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-import-type SelectOptionJson from SelectOption
 *
 * @psalm-type MultiSelectJson = array{
 *      id: string,
 *      name: string,
 *      type: "multi_select",
 *      multi_select: array{
 *          options: list<SelectOptionJson>
 *      },
 * }
 *
 * @psalm-immutable
 */
class MultiSelect implements PropertyInterface
{
    /** @param SelectOption[] $options */
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly array $options,
    ) {
    }

    /** @param SelectOption[] $options */
    public static function create(string $propertyName = "Multi Select", array $options = []): self
    {
        $property = PropertyMetadata::create("", $propertyName, PropertyType::MultiSelect);

        return new self($property, $options);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeOptions(SelectOption ...$options): self
    {
        return new self($this->metadata, $options);
    }

    public function addOption(SelectOption $option): self
    {
        $options = $this->options;
        $options[] = $option;

        return new self($this->metadata, $options);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var MultiSelectJson $array */
        $property = PropertyMetadata::fromArray($array);
        $options = array_map(
            fn($option) => SelectOption::fromArray($option),
            $array["multi_select"]["options"],
        );

        return new self($property, $options);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["multi_select"] = [
            "options" => array_map(fn(SelectOption $o) => $o->toArray(), $this->options),
        ];

        return $array;
    }
}
