<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type EmailJson = array{
 *      id: string,
 *      name: string,
 *      type: "email",
 *      email: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class Email implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "Email"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::Email);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var EmailJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["email"] = new \stdClass();

        return $array;
    }
}
