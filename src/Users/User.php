<?php

namespace Notion\Users;

/**
 * @psalm-import-type PersonJson from Person
 * @psalm-import-type BotJson from Bot
 *
 * @psalm-type UserJson = array{
 *     object: "user",
 *     id: string,
 *     name?: string,
 *     avatar_url?: string,
 *     type?: "person"|"bot",
 *     person?: PersonJson,
 *     bot?: BotJson,
 * }
 *
 * @psalm-immutable
 */
class User
{
    private function __construct(
        public readonly string $id,
        public readonly string|null $name,
        public readonly string|null $avatarUrl,
        public readonly UserType|null $type,
        public readonly Person|null $person,
        public readonly Bot|null $bot,
    ) {
    }

    /** @psalm-param UserJson $array */
    public static function fromArray(array $array): self
    {
        $person = array_key_exists("person", $array) ? Person::fromArray($array["person"]) : null;
        $bot = array_key_exists("bot", $array) ? Bot::fromArray($array["bot"]) : null;

        $name = array_key_exists("name", $array) ? $array["name"] : null;
        $avatarUrl = array_key_exists("avatar_url", $array) ? $array["avatar_url"] : null;
        $userType = array_key_exists("type", $array) ? UserType::from($array["type"]) : null;

        return new self(
            $array["id"],
            $name,
            $avatarUrl,
            $userType,
            $person,
            $bot,
        );
    }

    /** @psalm-return UserJson */
    public function toArray(): array
    {
        $array = [
            "object" => "user",
            "id"     => $this->id,
        ];

        if ($this->type !== null) {
            $array["type"] = $this->type->value;
        }

        if ($this->name !== null) {
            $array["name"] = $this->name;
        }

        if ($this->avatarUrl !== null) {
            $array["avatar_url"] = $this->avatarUrl;
        }

        if ($this->isPerson()) {
            $array["person"] = $this->person->toArray();
        }
        if ($this->isBot()) {
            $array["bot"] = $this->bot->toArray();
        }

        return $array;
    }

    /**
     * @psalm-assert-if-true Person $this->person
     */
    public function isPerson(): bool
    {
        return $this->type === UserType::Person;
    }

    /**
     * @psalm-assert-if-true Bot $this->bot
     */
    public function isBot(): bool
    {
        return $this->type === UserType::Bot;
    }
}
