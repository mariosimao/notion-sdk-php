<?php

namespace Notion\Pages\Properties;

use Notion\Databases\Properties\SelectOption;

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
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly SelectOption $option
    ) {
    }

    public static function fromId(string $id): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Select);
        $option = SelectOption::fromId($id);

        return new self($metadata, $option);
    }

    public static function fromName(string $name): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Select);
        $option = SelectOption::fromName($name);

        return new self($metadata, $option);
    }

    public static function fromOption(SelectOption $option): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Select);

        return new self($metadata, $option);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var SelectJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        $option = SelectOption::fromArray($array["select"] ?? []);

        return new self($metadata, $option);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["select"] = $this->option->toArray();

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeOption(SelectOption $option): self
    {
        return new self($this->metadata, $option);
    }
}
