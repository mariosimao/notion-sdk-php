<?php

namespace Notion\DataSources\Properties;

use Notion\Common\Color;

/**
 * @psalm-type StatusOptionJson = array{
 *      id?: string,
 *      name?: string,
 *      color?: string,
 *      description?: string|null,
 *      group?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class StatusOption
{
    private function __construct(
        public string|null $id,
        public string|null $name,
        public Color|null $color,
        public string|null $description = null,
        public string|null $group = null,
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
        $description = $array["description"] ?? null;
        $group = $array["group"] ?? null;

        return new self($id, $name, $color, $description, $group);
    }

    public function changeColor(Color $color): self
    {
        return new self($this->id, $this->name, $color, $this->description, $this->group);
    }

    public function changeName(string $name): self
    {
        return new self($this->id, $name, $this->color, $this->description, $this->group);
    }

    public function changeDescription(string $description): self
    {
        return new self($this->id, $this->name, $this->color, $description, $this->group);
    }

    /**
     * Place the option in a status group.
     *
     * Only used when writing the data source schema. Notion does not return
     * the group on options; use `Status::$groups` to read group membership.
     */
    public function changeGroup(StatusGroupName|string $group): self
    {
        $group = $group instanceof StatusGroupName ? $group->value : $group;

        return new self($this->id, $this->name, $this->color, $this->description, $group);
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
        if ($this->description !== null) {
            $option["description"] = $this->description;
        }
        if ($this->group !== null) {
            $option["group"] = $this->group;
        }

        return $option;
    }

    /**
     * Existing options are referenced by ID, as the API does not rename them.
     *
     * @internal
     */
    public function toRequestArray(): array
    {
        $option = $this->toArray();
        if ($this->id !== null) {
            unset($option["name"]);
        }

        return $option;
    }
}
