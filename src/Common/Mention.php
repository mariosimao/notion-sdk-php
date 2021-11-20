<?php

namespace Notion\Common;

use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 * @psalm-import-type DateJson from Date
 *
 * @psalm-type MentionJson = array{
 *      type: string,
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
    private const ALLOWED_TYPES = [ "page", "database", "user", "date" ];

    private string $type;
    private string|null $pageId;
    private string|null $databaseId;
    private User|null $user;
    private Date|null $date;

    private function __construct(
        string $type,
        string|null $pageId,
        string|null $databaseId,
        User|null $user,
        Date|null $date,
    ) {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new \Exception("Invalid mention type: '{$type}'.");
        }

        $this->type = $type;
        $this->pageId = $pageId;
        $this->databaseId = $databaseId;
        $this->user = $user;
        $this->date = $date;
    }

    public static function createPage(string $pageId): self
    {
        return new self("page", $pageId, null, null, null);
    }

    public static function createDatabase(string $databaseId): self
    {
        return new self("database", null, $databaseId, null, null);
    }

    public static function createUser(User $user): self
    {
        return new self("user", null, null, $user, null);
    }

    public static function createDate(Date $date): self
    {
        return new self("date", null, null, null, $date);
    }

    /**
     * @param MentionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = $array["type"];

        $pageId = array_key_exists("page", $array) ? $array["page"]["id"] : null;
        $databaseId = array_key_exists("database", $array) ? $array["database"]["id"] : null;
        $user = array_key_exists("user", $array) ? User::fromArray($array["user"]) : null;
        $date = array_key_exists("date", $array) ? Date::fromArray($array["date"]) : null;

        return new self($type, $pageId, $databaseId, $user, $date);
    }

    public function toArray(): array
    {
        $array = [ "type" => $this->type ];

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

    public function type(): string
    {
        return $this->type;
    }

    public function pageId(): string|null
    {
        return $this->pageId;
    }

    public function databaseId(): string|null
    {
        return $this->databaseId;
    }

    public function user(): User|null
    {
        return $this->user;
    }

    public function date(): Date|null
    {
        return $this->date;
    }

    /**
     * @psalm-assert-if-true string $this->pageId
     * @psalm-assert-if-true string $this->pageId()
     */
    public function isPage(): bool
    {
        return $this->type === "page";
    }

    /**
     * @psalm-assert-if-true string $this->databaseId
     * @psalm-assert-if-true string $this->databaseId()
     */
    public function isDatabase(): bool
    {
        return $this->type === "database";
    }

    /**
     * @psalm-assert-if-true User $this->user
     * @psalm-assert-if-true User $this->user()
     */
    public function isUser(): bool
    {
        return $this->type === "user";
    }

    /**
     * @psalm-assert-if-true Date $this->date
     * @psalm-assert-if-true Date $this->date()
     */
    public function isDate(): bool
    {
        return $this->type === "date";
    }
}
