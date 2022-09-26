<?php

namespace Notion\Users;

/**
 * @psalm-import-type PersonJson from Person
 * @psalm-import-type BotJson from Bot
 *
 * @psalm-type UserJson = array{
 *     id: string,
 *     name: string,
 *     avatar_url: string|null,
 *     type: "person"|"bot",
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
        public readonly string $name,
        public readonly string|null $avatarUrl,
        public readonly UserType $type,
        public readonly Person|null $person,
        public readonly Bot|null $bot,
    ) {
    }

    /** @psalm-param UserJson $array */
    public static function fromArray(array $array): self
    {
        $person = array_key_exists("person", $array) ? Person::fromArray($array["person"]) : null;
        $bot = array_key_exists("bot", $array) ? Bot::fromArray($array["bot"]) : null;

        return new self(
            $array["id"],
            $array["name"],
            $array["avatar_url"],
            UserType::from($array["type"]),
            $person,
            $bot,
        );
    }

    /** @psalm-return UserJson */
    public function toArray(): array
    {
        $array = [
            "id"         => $this->id,
            "name"       => $this->name,
            "avatar_url" => $this->avatarUrl,
            "type"       => $this->type->value,
        ];

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
