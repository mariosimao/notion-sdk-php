<?php

namespace Notion\Pages;

use Notion\DataSources\Template;

/**
 * Template applied when creating or updating a page
 *
 * @psalm-type PageTemplateJson = array{
 *      type: "none"|"default"|"template_id",
 *      template_id?: string,
 *      timezone?: string
 * }
 *
 * @psalm-immutable
 */
final readonly class PageTemplate
{
    private function __construct(
        public PageTemplateType $type,
        public string|null $templateId,
        public string|null $timezone,
    ) {
    }

    public static function none(): self
    {
        return new self(PageTemplateType::None, null, null);
    }

    /**
     * Apply the data source's default template
     *
     * @param string|null $timezone IANA timezone used to resolve template variables like `@now` and `@today`
     */
    public static function default(string|null $timezone = null): self
    {
        return new self(PageTemplateType::Default, null, $timezone);
    }

    /** @param string|null $timezone IANA timezone used to resolve template variables like `@now` and `@today` */
    public static function fromId(string $templateId, string|null $timezone = null): self
    {
        return new self(PageTemplateType::TemplateId, $templateId, $timezone);
    }

    /** @param string|null $timezone IANA timezone used to resolve template variables like `@now` and `@today` */
    public static function fromTemplate(Template $template, string|null $timezone = null): self
    {
        return self::fromId($template->id, $timezone);
    }

    public function isNone(): bool
    {
        return $this->type === PageTemplateType::None;
    }

    public function isDefault(): bool
    {
        return $this->type === PageTemplateType::Default;
    }

    public function isTemplateId(): bool
    {
        return $this->type === PageTemplateType::TemplateId;
    }

    /** @return PageTemplateJson */
    public function toArray(): array
    {
        $array = [ "type" => $this->type->value ];

        if ($this->templateId !== null) {
            $array["template_id"] = $this->templateId;
        }

        if ($this->timezone !== null) {
            $array["timezone"] = $this->timezone;
        }

        return $array;
    }
}
