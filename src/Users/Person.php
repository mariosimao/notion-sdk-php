<?php

namespace Notion\Users;

class Person
{
    private string $email;

    private function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function fromArray(array $array): self
    {
        return new self($array["email"]);
    }

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
