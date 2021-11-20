<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type SelectOptionJson = array{
 *      id: string,
 *      name: string,
 *      color: string,
 * }
 *
 * @psalm-immutable
 */
class SelectOption
{
    public const COLOR_DEFAULT = "default";
    public const COLOR_GRAY = "gray";
    public const COLOR_BROWN = "brown";
    public const COLOR_ORANGE = "orange";
    public const COLOR_YELLOW = "yellow";
    public const COLOR_GREEN = "green";
    public const COLOR_BLUE = "blue";
    public const COLOR_PURPLE = "purple";
    public const COLOR_PINK = "pink";
    public const COLOR_RED = "red";

    private string $id;
    private string $name;
    private string $color;

    private function __construct(string $id, string $name, string $color)
    {
        $this->id = $id;
        $this->name = $name;
        $this->color = $color;
    }

    public static function create(string $name, string $color = "default"): self
    {
        return new self("", $name, $color);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function color(): string
    {
        return $this->color;
    }

    public function withName(string $newName): self
    {
        return new self($this->id, $newName, $this->color);
    }

    public function withColor(string $newColor): self
    {
        return new self($this->id, $this->name, $newColor);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var SelectOptionJson $array */
        return new self(
            $array["id"],
            $array["name"],
            $array["color"],
        );
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "color" => $this->color,
        ];
    }
}
