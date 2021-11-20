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
 *          options: list<list<SelectOptionJson>
 *      },
 * }
 *
 * @psalm-immutable
 */
class MultiSelect implements PropertyInterface
{
    private const TYPE = Property::TYPE_MULTI_SELECT;

    private Property $property;
    /** @var list<SelectOption> */
    private array $options;

    /** @param list<SelectOption> $options */
    private function __construct(Property $property, array $options)
    {
        $this->property = $property;
        $this->options = $options;
    }

    /** @param list<SelectOption> $options */
    public static function create(string $propertyName = "Multi Select", array $options = []): self
    {
        $property = Property::create("", $propertyName, self::TYPE);

        return new self($property, $options);
    }

    public function property(): Property
    {
        return $this->property;
    }

    /** @return list<SelectOption> */
    public function options(): array
    {
        return $this->options;
    }

    /** @param list<SelectOption> $options */
    public function withOptions(array $options): self
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
        /** @psalm-var MultiSelectJson $array */
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
