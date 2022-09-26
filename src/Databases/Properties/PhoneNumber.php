<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type PhoneNumberJson = array{
 *      id: string,
 *      name: string,
 *      type: "phone_number",
 *      phone_number: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class PhoneNumber implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "PhoneNumber"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::PhoneNumber);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var PhoneNumberJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["phone_number"] = new \stdClass();

        return $array;
    }
}
