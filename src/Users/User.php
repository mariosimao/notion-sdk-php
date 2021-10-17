<?php

namespace Notion\Users;

class User
{
    private const ALLOWED_TYPES = [ "person", "bot" ];

    private string $id;
    private string $name;
    private string|null $avatarUrl;
    private string $type;
    private Person|null $person;
    private Bot|null $bot;

    private function __construct(
        string $id,
        string $name,
        string|null $avatarUrl,
        string $type,
        Person|null $person,
        Bot|null $bot,
    ) {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new \Exception("Invalid user type: '{$type}'.");
        }

        $this->id = $id;
        $this->name = $name;
        $this->avatarUrl = $avatarUrl;
        $this->type = $type;
        $this->person = $person;
        $this->bot = $bot;
    }

    public static function fromArray(array $array): self
    {
        $person = array_key_exists("person", $array) ? Person::fromArray($array["person"]) : null;
        $bot = array_key_exists("bot", $array) ? Bot::fromArray($array["bot"]) : null;

        return new self(
            $array["id"],
            $array["name"],
            $array["avatar_url"],
            $array["type"],
            $person,
            $bot,
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function avatarUrl(): string|null
    {
        return $this->avatarUrl;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function person(): Person|null
    {
        return $this->person;
    }

    public function bot(): Bot|null
    {
        return $this->bot;
    }

    public function isPerson(): bool
    {
        return $this->type === "person";
    }

    public function isBot(): bool
    {
        return $this->type === "bot";
    }
}
