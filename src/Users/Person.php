<?php

namespace Notion\Users;

/**
 * @psalm-type PersonJson = array{email: string, email_verified?: bool}
 *
 * @psalm-immutable
 */
final readonly class Person
{
    private function __construct(
        public string $email,
        /** Whether Notion has verified the email address. `null` when not returned by the API. */
        public bool|null $emailVerified = null,
    ) {
    }

    /** @param PersonJson $array */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["email"],
            $array["email_verified"] ?? null,
        );
    }

    /** @return PersonJson */
    public function toArray(): array
    {
        $array = [
            "email" => $this->email,
        ];

        if ($this->emailVerified !== null) {
            $array["email_verified"] = $this->emailVerified;
        }

        return $array;
    }

    /** @psalm-assert-if-true true $this->emailVerified */
    public function isEmailVerified(): bool
    {
        return $this->emailVerified === true;
    }
}
