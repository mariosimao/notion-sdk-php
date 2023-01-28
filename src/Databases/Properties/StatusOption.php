<?php

namespace Notion\Databases\Properties;

use Notion\Common\Color;

/**
 * @psalm-type StatusOptionJson = array{
 *      id?: string,
 *      name?: string,
 *      color?: string
 * }
 *
 * @psalm-immutable
 */
class StatusOption
{
    private function __construct(
        public readonly string|null $id,
        public readonly string|null $name,
        public readonly Color|null $color,
    ) {
    }

    public static function fromId(string $id): self
    {
        return new self($id, null, null);
    }

    public static function fromName(string $name): self
    {
        return new self(null, $name, null);
    }

    public static function fromArray(array $array): self
    {
        /** @var StatusOptionJson $array */

        $id = $array["id"] ?? null;
        $name = $array["name"] ?? null;
        $color = Color::tryFrom($array["color"] ?? "");

        return new self($id, $name, $color);
    }

    public function changeColor(Color $color): self
    {
        return new self($this->id, $this->name, $color);
    }

    public function toArray(): array
    {
        $option = [];

        if ($this->name !== null) {
            $option["name"] = $this->name;
        }
        if ($this->id !== null) {
            $option["id"] = $this->id;
        }
        if ($this->color !== null) {
            $option["color"] = $this->color->value;
        }

        return $option;
    }
}
