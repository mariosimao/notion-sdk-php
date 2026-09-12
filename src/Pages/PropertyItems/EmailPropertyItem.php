<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type EmailItemJson = array{
 *      id: string,
 *      type: "email",
 *      email: string|null,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class EmailPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public string|null $email,
    ) {
    }

    public static function create(string|null $email, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Email);

        return new self($metadata, $email);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var EmailItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $email = $array["email"] ?? null;

        return new self($metadata, $email);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["email"] = $this->email;

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return $this->email === null;
    }
}
