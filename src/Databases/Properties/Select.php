<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-import-type SelectOptionJson from SelectOption
 *
 * @psalm-type SelectJson = array{
 *      id: string,
 *      name: string,
 *      type: "select",
 *      select: array{
 *          options: list<SelectOptionJson>
 *      },
 * }
 *
 * @psalm-immutable
 */
class Select implements PropertyInterface
{
    /** @param SelectOption[] $options */
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly array $options
    ) {
    }

    /** @param SelectOption[] $options */
    public static function create(string $propertyName = "Select", array $options = []): self
    {
        $property = PropertyMetadata::create("", $propertyName, PropertyType::Select);

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
        /** @psalm-var SelectJson $array */
        $property = PropertyMetadata::fromArray($array);
        $options = array_map(
            function (array $option): SelectOption {
                return SelectOption::fromArray($option);
            },
            $array["select"]["options"],
        );

        return new self($property, $options);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["select"] = [
            "options" => array_map(fn(SelectOption $o) => $o->toArray(), $this->options),
        ];

        return $array;
    }
}
