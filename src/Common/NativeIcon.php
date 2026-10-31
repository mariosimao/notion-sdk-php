<?php

namespace Notion\Common;

/**
 * Built-in Notion icon, identified by name and color.
 *
 * @psalm-type NativeIconJson = array{
 *      type: "icon",
 *      icon: array{ name: string, color?: string },
 * }
 *
 * @psalm-immutable
 */
final readonly class NativeIcon
{
    private function __construct(
        public string $name,
        public NativeIconColor $color,
    ) {
    }

    /**
     * The name accepts either the internal API icon name (e.g. "pizza") or
     * the name shown in the Notion icon picker (e.g. "star circle").
     */
    public static function create(string $name, NativeIconColor $color = NativeIconColor::Gray): self
    {
        return new self($name, $color);
    }

    /** @param NativeIconJson $array */
    public static function fromArray(array $array): self
    {
        $icon = $array["icon"];
        $color = NativeIconColor::tryFrom($icon["color"] ?? "") ?? NativeIconColor::Gray;

        return new self($icon["name"], $color);
    }

    public function toArray(): array
    {
        return [
            "type" => "icon",
            "icon" => [
                "name"  => $this->name,
                "color" => $this->color->value,
            ],
        ];
    }

    public function changeName(string $name): self
    {
        return new self($name, $this->color);
    }

    public function changeColor(NativeIconColor $color): self
    {
        return new self($this->name, $color);
    }
}
