<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type PhoneNumberItemJson = array{
 *      id: string,
 *      type: "phone_number",
 *      phone_number: string|null,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class PhoneNumberPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public string|null $phoneNumber,
    ) {
    }

    public static function create(string|null $phoneNumber, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::PhoneNumber);

        return new self($metadata, $phoneNumber);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var PhoneNumberItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $phoneNumber = $array["phone_number"] ?? null;

        return new self($metadata, $phoneNumber);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["phone_number"] = $this->phoneNumber;

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return $this->phoneNumber === null;
    }
}
