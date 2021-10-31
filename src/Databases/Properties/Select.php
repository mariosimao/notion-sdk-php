<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-import-type SelectOptionJson from SelectOption
 *
 * @psalm-type SelectJson = array{
 *      id: string,
 *      name: string,
 *      type: "select",
 *      select: array{ options: SelectOptionJson[] },
 * }
 */
class Select implements PropertyInterface
{
    private const TYPE = Property::TYPE_SELECT;

    private Property $property;
    /** @var SelectOption[] */
    private array $options;

    /** @param SelectOption[] $options */
    private function __construct(Property $property, array $options)
    {
        $this->property = $property;
        $this->options = $options;
    }

    /** @param SelectOption[] $options */
    public static function create(string $propertyName = "Select", array $options = []): self
    {
        $property = Property::create("", $propertyName, self::TYPE);

        return new self($property, $options);
    }

    public function property(): Property
    {
        return $this->property;
    }

    /** @return SelectOption[] */
    public function options(): array
    {
        return $this->options;
    }

    public function withOptions(SelectOption ...$options): self
    {
        return new self($this->property, $options);
    }

    public function addOption(SelectOption $option): self
    {
        $options = $this->options;
        $options[] = $option;

        return new self($this->property, $options);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var SelectJson $array */
        $property = Property::fromArray($array);
        $options = array_map(
            function (array $option): SelectOption {
                return SelectOption::fromArray($option);
            },
            $array[self::TYPE]["options"],
        );

        return new self($property, $options);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();
        $array[self::TYPE] = [
            "options" => array_map(fn(SelectOption $o) => $o->toArray(), $this->options),
        ];

        return $array;
    }
}
