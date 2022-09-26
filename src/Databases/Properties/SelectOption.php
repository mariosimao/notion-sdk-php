<?php

namespace Notion\Databases\Properties;

use Notion\Common\Color;

/**
 * @psalm-type SelectOptionJson = array{
 *      id?: string,
 *      name?: string,
 *      color: string
 * }
 *
 * @psalm-immutable
 */
class SelectOption
{
    private function __construct(
        public readonly string|null $id,
        public readonly string|null $name,
        public readonly Color $color,
    ) {}

    public static function fromId(string $id): self
    {
        return new self($id, null, Color::Default);
    }

    public static function fromName(string $name): self
    {
        return new self(null, $name, Color::Default);
    }

    public static function fromArray(array $array): self
    {
        /** @var SelectOptionJson $array */

        $id = $array["id"] ?? null;
        $name = $array["name"] ?? null;
        $color = Color::tryFrom($array["color"]) ?? Color::Default;

        return new self($id, $name, $color);
    }

    public function toArray(): array
    {
        $option = [ "color" => $this->color->value ];

        if ($this->name !== null) {
            $option["name"] = $this->name;
        }
        if ($this->id !== null) {
            $option["id"] = $this->id;
        }

        return $option;
    }

    public function changeName(string $name): self
    {
        return new self($this->id, $name, $this->color);
    }

    public function changeColor(Color $color): self
    {
        return new self($this->id, $this->name, $color);
    }
}
