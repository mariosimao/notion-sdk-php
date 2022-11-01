<?php

namespace Notion\Common;

use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 * @psalm-import-type DateJson from Date
 *
 * @psalm-type MentionJson = array{
 *      type: "page"|"database"|"user"|"date",
 *      page?: array{ id: string },
 *      database?: array{ id: string },
 *      user?: UserJson,
 *      date?: DateJson,
 * }
 *
 * @psalm-immutable
 */
class Mention
{
    private function __construct(
        public readonly MentionType $type,
        public readonly string|null $pageId,
        public readonly string|null $databaseId,
        public readonly User|null $user,
        public readonly Date|null $date,
    ) {
    }

    public static function page(string $pageId): self
    {
        return new self(MentionType::Page, $pageId, null, null, null);
    }

    public static function database(string $databaseId): self
    {
        return new self(MentionType::Database, null, $databaseId, null, null);
    }

    public static function user(User $user): self
    {
        return new self(MentionType::User, null, null, $user, null);
    }

    public static function date(Date $date): self
    {
        return new self(MentionType::Date, null, null, null, $date);
    }

    /**
     * @psalm-param MentionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = MentionType::from($array["type"]);

        $pageId = array_key_exists("page", $array) ? $array["page"]["id"] : null;
        $databaseId = array_key_exists("database", $array) ? $array["database"]["id"] : null;
        $user = array_key_exists("user", $array) ? User::fromArray($array["user"]) : null;
        $date = array_key_exists("date", $array) ? Date::fromArray($array["date"]) : null;

        return new self($type, $pageId, $databaseId, $user, $date);
    }

    public function toArray(): array
    {
        $array = [ "type" => $this->type->value ];

        if ($this->isPage()) {
            $array["page"] = [ "id" => $this->pageId ];
        }
        if ($this->isDatabase()) {
            $array["database"] = [ "id" => $this->databaseId ];
        }
        if ($this->isUser()) {
            $array["user"] = $this->user->toArray();
        }
        if ($this->isDate()) {
            $array["date"] = $this->date->toArray();
        }

        return $array;
    }

    /**
     * @psalm-assert-if-true string $this->pageId
     */
    public function isPage(): bool
    {
        return $this->type === MentionType::Page;
    }

    /**
     * @psalm-assert-if-true string $this->databaseId
     */
    public function isDatabase(): bool
    {
        return $this->type === MentionType::Database;
    }

    /**
     * @psalm-assert-if-true User $this->user
     */
    public function isUser(): bool
    {
        return $this->type === MentionType::User;
    }

    /**
     * @psalm-assert-if-true Date $this->date
     */
    public function isDate(): bool
    {
        return $this->type === MentionType::Date;
    }
}
