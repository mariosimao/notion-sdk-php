<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type CheckboxJson = array{
 *      id: string,
 *      type: "checkbox",
 *      checkbox: bool,
 * }
 *
 * @psalm-immutable
 */
class Checkbox implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly bool $checked,
    ) {
    }

    public static function createChecked(): self
    {
        $property = PropertyMetadata::create("", PropertyType::Checkbox);

        return new self($property, true);
    }

    public static function createUnchecked(): self
    {
        $property = PropertyMetadata::create("", PropertyType::Checkbox);

        return new self($property, false);
    }

    public static function createEmpty(string $id = null): self
    {
        $property = PropertyMetadata::create($id ?? "", PropertyType::Checkbox);

        return new self($property, false);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CheckboxJson $array */

        $property = PropertyMetadata::fromArray($array);

        $checked = $array["checkbox"];

        return new self($property, $checked);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["checkbox"] = $this->checked;

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function check(): self
    {
        return new self($this->metadata, true);
    }

    public function uncheck(): self
    {
        return new self($this->metadata, false);
    }
}
