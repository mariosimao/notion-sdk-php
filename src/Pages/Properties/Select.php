<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type SelectJson = array{
 *      id: string,
 *      type: "select",
 *      select: array{ id: string, name: string, color: string }
 * }
 *
 * @psalm-immutable
 */
class Select implements PropertyInterface
{
    private const TYPE = Property::TYPE_SELECT;

    private Property $property;

    private Option $option;

    private function __construct(Property $property, Option $option)
    {
        $this->property = $property;
        $this->option = $option;
    }

    public static function fromId(string $id): self
    {
        $property = Property::create("", self::TYPE);
        $option = Option::fromId($id);

        return new self($property, $option);
    }

    public static function fromName(string $name): self
    {
        $property = Property::create("", self::TYPE);
        $option = Option::fromName($name);

        return new self($property, $option);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var SelectJson $array */
        $property = Property::fromArray($array);

        $option = Option::fromArray($array[self::TYPE]);

        return new self($property, $option);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();
        $array[self::TYPE] = $this->option->toArray();

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function id(): string|null
    {
        return $this->option->id();
    }

    public function withId(string $id): self
    {
        $option = $this->option->withId($id);

        return new self($this->property, $option);
    }

    public function name(): string|null
    {
        return $this->option->name();
    }

    public function withName(string $name): self
    {
        $option = $this->option->withName($name);

        return new self($this->property, $option);
    }

    public function color(): string
    {
        return $this->option->color();
    }

    public function withColor(string $color): self
    {
        $option = $this->option->withColor($color);

        return new self($this->property, $option);
    }
}
