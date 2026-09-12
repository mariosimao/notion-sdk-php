<?php

namespace Notion\Common;

/**
 * @psalm-type TemplateMentionJson = array{
 *      type: "template_mention_date"|"template_mention_user",
 *      template_mention_date?: string,
 *      template_mention_user?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class TemplateMention
{
    /**
     * @param array<string, mixed> $raw
     */
    private function __construct(
        public TemplateMentionType $type,
        public TemplateMentionDateType|null $templateMentionDate = null,
        public TemplateMentionUserType|null $templateMentionUser = null,
        public array $raw = [],
    ) {
    }

    public static function date(TemplateMentionDateType $date): self
    {
        return new self(TemplateMentionType::Date, templateMentionDate: $date);
    }

    public static function user(TemplateMentionUserType $user = TemplateMentionUserType::Me): self
    {
        return new self(TemplateMentionType::User, templateMentionUser: $user);
    }

    public static function today(): self
    {
        return self::date(TemplateMentionDateType::Today);
    }

    public static function now(): self
    {
        return self::date(TemplateMentionDateType::Now);
    }

    public static function me(): self
    {
        return self::user(TemplateMentionUserType::Me);
    }

    /**
     * @psalm-param TemplateMentionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = TemplateMentionType::from($array["type"]);

        $date = isset($array["template_mention_date"])
            ? TemplateMentionDateType::from($array["template_mention_date"])
            : null;

        $user = isset($array["template_mention_user"])
            ? TemplateMentionUserType::from($array["template_mention_user"])
            : null;

        /** @var array<string, mixed> $raw */
        $raw = $array;

        return new self($type, $date, $user, $raw);
    }

    public function toArray(): array
    {
        /** @var array<string, mixed> $array */
        $array = array_merge($this->raw, [ "type" => $this->type->value ]);

        if ($this->isDate()) {
            $array["template_mention_date"] = $this->templateMentionDate->value;
        }

        if ($this->isUser()) {
            $array["template_mention_user"] = $this->templateMentionUser->value;
        }

        return $array;
    }

    /**
     * @psalm-assert-if-true TemplateMentionDateType $this->templateMentionDate
     */
    public function isDate(): bool
    {
        return $this->type === TemplateMentionType::Date;
    }

    /**
     * @psalm-assert-if-true TemplateMentionUserType $this->templateMentionUser
     */
    public function isUser(): bool
    {
        return $this->type === TemplateMentionType::User;
    }
}
