<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type EmailJson = array{
 *      id: string,
 *      type: "email",
 *      email: string|null,
 * }
 *
 * @psalm-immutable
 */
class Email implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly string|null $email,
    ) {
    }

    public static function create(string $email): self
    {
        $property = PropertyMetadata::create("", PropertyType::Email);

        return new self($property, $email);
    }

    public static function createEmpty(string $id = null): self
    {
        $property = PropertyMetadata::create($id ?? "", PropertyType::Email);

        return new self($property, null);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var EmailJson $array */

        $property = PropertyMetadata::fromArray($array);

        $email = $array["email"];

        return new self($property, $email);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["email"] = $this->email;

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeEmail(string $email): self
    {
        return new self($this->metadata, $email);
    }

    public function clear(): self
    {
        return new self($this->metadata, null);
    }

    public function isEmpty(): bool
    {
        return $this->email === null;
    }
}
