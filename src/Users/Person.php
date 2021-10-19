<?php

namespace Notion\Users;

/**
 * @psalm-type PersonJson = array{email: string}
 */
class Person
{
    private string $email;

    private function __construct(string $email)
    {
        $this->email = $email;
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

    public function email(): string
    {
        return $this->email;
    }
}
