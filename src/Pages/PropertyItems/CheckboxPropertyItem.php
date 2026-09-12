<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type CheckboxItemJson = array{
 *      id: string,
 *      type: "checkbox",
 *      checkbox: bool,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class CheckboxPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public bool $checkbox,
    ) {
    }

    public static function create(bool $checkbox, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Checkbox);

        return new self($metadata, $checkbox);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CheckboxItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $checkbox = $array["checkbox"];

        return new self($metadata, $checkbox);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["checkbox"] = $this->checkbox;

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isChecked(): bool
    {
        return $this->checkbox;
    }
}
