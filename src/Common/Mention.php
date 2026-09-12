<?php

namespace Notion\Common;

use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 * @psalm-import-type DateJson from Date
 * @psalm-import-type TemplateMentionJson from TemplateMention
 *
 * @psalm-type MentionJson = array{
 *      type: "page"|"database"|"user"|"date"|"link_preview"|"template_mention",
 *      page?: array{ id: string },
 *      database?: array{ id: string },
 *      user?: UserJson,
 *      date?: DateJson,
 *      link_preview?: array{ url: string },
 *      template_mention?: TemplateMentionJson,
 * }
 *
 * @psalm-immutable
 */
final readonly class Mention
{
    private function __construct(
        public MentionType $type,
        public string|null $pageId,
        public string|null $databaseId,
        public User|null $user,
        public Date|null $date,
        public string|null $linkPreviewUrl = null,
        public TemplateMention|null $templateMention = null,
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

    public static function linkPreview(string $url): self
    {
        return new self(
            MentionType::LinkPreview,
            null,
            null,
            null,
            null,
            linkPreviewUrl: $url,
        );
    }

    public static function templateMention(TemplateMention $templateMention): self
    {
        return new self(
            MentionType::TemplateMention,
            null,
            null,
            null,
            null,
            templateMention: $templateMention,
        );
    }

    public static function templateDate(TemplateMentionDateType $date): self
    {
        return self::templateMention(TemplateMention::date($date));
    }

    public static function templateUser(TemplateMentionUserType $user = TemplateMentionUserType::Me): self
    {
        return self::templateMention(TemplateMention::user($user));
    }

    public static function today(): self
    {
        return self::templateMention(TemplateMention::today());
    }

    public static function now(): self
    {
        return self::templateMention(TemplateMention::now());
    }

    public static function me(): self
    {
        return self::templateMention(TemplateMention::me());
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
        $linkPreviewUrl = array_key_exists("link_preview", $array) ? $array["link_preview"]["url"] : null;
        $templateMention = array_key_exists("template_mention", $array)
            ? TemplateMention::fromArray($array["template_mention"])
            : null;

        return new self($type, $pageId, $databaseId, $user, $date, $linkPreviewUrl, $templateMention);
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
        if ($this->isLinkPreview()) {
            $array["link_preview"] = [ "url" => $this->linkPreviewUrl ];
        }
        if ($this->isTemplateMention()) {
            $array["template_mention"] = $this->templateMention->toArray();
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

    /**
     * @psalm-assert-if-true string $this->linkPreviewUrl
     */
    public function isLinkPreview(): bool
    {
        return $this->type === MentionType::LinkPreview;
    }

    /**
     * @psalm-assert-if-true TemplateMention $this->templateMention
     */
    public function isTemplateMention(): bool
    {
        return $this->type === MentionType::TemplateMention;
    }
}
