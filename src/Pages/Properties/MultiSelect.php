<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-import-type OptionJson from Option
 *
 * @psalm-type MultiSelectJson = array{
 *      id: string,
 *      type: "multi_select",
 *      multi_select: list<OptionJson>,
 * }
 *
 * @psalm-immutable
 */
class MultiSelect implements PropertyInterface
{
    private const TYPE = Property::TYPE_MULTI_SELECT;

    private Property $property;

    /** @var list<Option> */
    private array $options;

    /** @param list<Option> $options */
    private function __construct(Property $property, array $options)
    {
        $this->property = $property;
        $this->options = $options;
    }

    /** @param list<non-empty-string> $ids */
    public static function fromIds(array $ids): self
    {
        $property = Property::create("", self::TYPE);
        $options = array_map(fn(string $id) => Option::fromId($id), $ids);

        return new self($property, $options);
    }

    /** @param list<non-empty-string> $names */
    public static function fromNames(array $names): self
    {
        $property = Property::create("", self::TYPE);
        $options = array_map(fn(string $name) => Option::fromName($name), $names);

        return new self($property, $options);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var MultiSelectJson $array */
        $property = Property::fromArray($array);

        $options = array_map(fn(array $option) => Option::fromArray($option), $array[self::TYPE]);

        return new self($property, $options);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();
        $array[self::TYPE] = array_map(fn (Option $option) => $option->toArray(), $this->options);

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    /** @return list<Option> */
    public function options(): array
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        $options = $this->options;
        $options[] = $option;

        return new self($this->property, $options);
    }
}
