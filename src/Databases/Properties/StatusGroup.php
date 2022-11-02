<?php

namespace Notion\Databases\Properties;

use Notion\Common\Color;

/**
 * @psalm-type StatusGroupJson = array{
 *      id?: string,
 *      name?: string,
 *      color: string,
 *      option_ids: string[]
 * }
 *
 * @psalm-immutable
 */
class StatusGroup
{
    /** @param string[] $optionIds */
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly Color $color,
        public array $optionIds,
    ) {
    }

    public static function fromArray(array $array): self
    {
        /** @var StatusGroupJson $array */
        $id = $array["id"] ?? "";
        $name = $array["name"] ?? "";
        $color = Color::tryFrom($array["color"]) ?? Color::Default;
        $optionIds = $array["option_ids"];

        return new self($id, $name, $color, $optionIds);
    }

    public function toArray(): array
    {
        return [
            "id"         => $this->id,
            "name"       => $this->name,
            "color"      => $this->color->value,
            "option_ids" => $this->optionIds,
        ];
    }
}
