<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type OptionJson = array{
 *      id?: string,
 *      name?: string,
 *      color: string
 * }
 *
 * @psalm-immutable
 */
class Option
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

    private string|null $id;
    private string|null $name;
    private string $color;

    private function __construct(string|null $id, string|null $name, string $color)
    {
        $this->id = $id;
        $this->name = $name;
        $this->color = $color;
    }

    public static function fromId(string $id): self
    {
        return new self($id, null, self::COLOR_DEFAULT);
    }

    public static function fromName(string $name): self
    {
        return new self(null, $name, self::COLOR_DEFAULT);
    }

    public static function fromArray(array $array): self
    {
        /** @var OptionJson $array */

        $id = $array["id"] ?? null;
        $name = $array["name"] ?? null;
        $color = $array["color"];

        return new self($id, $name, $color);
    }

    public function toArray(): array
    {
        $option = [ "color" => $this->color ];

        if ($this->name !== null) {
            $option["name"] = $this->name;
        }
        if ($this->id !== null) {
            $option["id"] = $this->id;
        }

        return $option;
    }

    public function id(): string|null
    {
        return $this->id;
    }

    public function name(): string|null
    {
        return $this->name;
    }

    public function color(): string
    {
        return $this->color;
    }

    public function withId(string $id): self
    {
        return new self($id, $this->name, $this->color);
    }

    public function withName(string $name): self
    {
        return new self($this->id, $name, $this->color);
    }

    public function withColor(string $color): self
    {
        return new self($this->id, $this->name, $color);
    }
}
