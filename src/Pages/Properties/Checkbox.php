<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type CheckboxJson = array{
 *      id: string,
 *      type: "checkbox",
 *      checkbox: bool,
 * }
 *
 * @psalm-immutable
 */
class Checkbox implements PropertyInterface
{
    private const TYPE = Property::TYPE_CHECKBOX;

    private Property $property;

    private bool $checked;

    private function __construct(Property $property, bool $checked)
    {
        $this->property = $property;
        $this->checked = $checked;
    }

    public static function create(bool $checked = false): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, $checked);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CheckboxJson $array */

        $property = Property::fromArray($array);

        $checked = $array[self::TYPE];

        return new self($property, $checked);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = $this->checked;

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function check(): self
    {
        return new self($this->property, true);
    }

    public function uncheck(): self
    {
        return new self($this->property, false);
    }
}
