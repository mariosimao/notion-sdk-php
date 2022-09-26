<?php

namespace Notion\Users;

/**
 * @psalm-type PersonJson = array{email: string}
 *
 * @psalm-immutable
 */
class Person
{
    private function __construct(
        public readonly string $email,
    ) {
    }

    /** @param PersonJson $array */
    public static function fromArray(array $array): self
    {
        return new self($array["email"]);
    }

    /** @return PersonJson */
    public function toArray(): array
    {
        return [
            "email" => $this->email,
        ];
    }
}
