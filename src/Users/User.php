<?php

namespace Notion\Users;

use Assert\Assert;

/**
 * @psalm-import-type PersonJson from Person
 * @psalm-import-type BotJson from Bot
 *
 * @psalm-type UserJson = array{
 *     id: string,
 *     name: string,
 *     avatar_url: string|null,
 *     type: string,
 *     person?: PersonJson,
 *     bot?: BotJson,
 * }
 */
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

    /** @param UserJson $array */
    public static function fromArray(array $array): self
    {
        Assert::that($array)->keyExists("id");
        Assert::that($array)->keyExists("name");
        Assert::that($array)->keyExists("avatar_url");
        Assert::that($array)->keyExists("type");

        Assert::that($array["id"])->string();
        Assert::that($array["name"])->string();
        Assert::that($array["avatar_url"])->string();
        Assert::that($array["type"])->string();

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

    /** @return UserJson */
    public function toArray(): array
    {
        $array = [
            "id"         => $this->id,
            "name"       => $this->name,
            "avatar_url" => $this->avatarUrl,
            "type"       => $this->type,
        ];

        if ($this->isPerson()) {
            $array["person"] = $this->person->toArray();
        }
        if ($this->isBot()) {
            $array["bot"] = $this->bot->toArray();
        }

        return $array;
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

    /**
     * @psalm-assert-if-true Person $this->person
     * @psalm-assert-if-true Person $this->person()
     */
    public function isPerson(): bool
    {
        return $this->type === "person";
    }

    /**
     * @psalm-assert-if-true Bot $this->bot
     * @psalm-assert-if-true Bot $this->bot()
     */
    public function isBot(): bool
    {
        return $this->type === "bot";
    }
}
