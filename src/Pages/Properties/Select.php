<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type SelectJson = array{
 *      id: string,
 *      type: "select",
 *      select: array{ id: string, name: string, color: string }
 * }
 */
class Select implements PropertyInterface
{
    public const COLOR_DEFAULT = "default";
    public const COLOR_GRAY    = "gray";
    public const COLOR_BROWN   = "brown";
    public const COLOR_RED     = "red";
    public const COLOR_ORANGE  = "orange";
    public const COLOR_YELLOW  = "yellow";
    public const COLOR_GREEN   = "green";
    public const COLOR_BLUE    = "blue";
    public const COLOR_PURPLE  = "purple";
    public const COLOR_PINK    = "pink";

    private const TYPE = Property::TYPE_SELECT;

    private Property $property;

    private string|null $id;
    private string|null $name;

    private function __construct(Property $property, string|null $id, string|null $name)
    {
        $this->property = $property;
        $this->id = $id;
        $this->name = $name;
    }

    public static function fromId(string $id): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, $id, null);
    }

    public static function fromName(string $name): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, null, $name);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var SelectJson $array */

        $property = Property::fromArray($array);

        $id = $array[self::TYPE]["id"] ?? null;
        $name = $array[self::TYPE]["name"] ?? null;

        return new self($property, $id, $name);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $select = [];
        if ($this->name !== null) {
            $select["name"] = $this->name;
        }
        if ($this->id !== null) {
            $select["id"] = $this->id;
        }
        $array[self::TYPE] = $select;

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function id(): string|null
    {
        return $this->id;
    }

    public function withId(string $id): self
    {
        return new self($this->property, $id, $this->name);
    }

    public function name(): string|null
    {
        return $this->name;
    }

    public function withName(string $name): self
    {
        return new self($this->property, $this->id, $name);
    }
}
