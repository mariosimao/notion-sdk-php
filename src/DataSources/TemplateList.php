<?php

namespace Notion\DataSources;

/**
 * Paginated list of data source templates
 *
 * @psalm-import-type TemplateJson from Template
 *
 * @psalm-type TemplateListJson = array{
 *      templates: list<TemplateJson>,
 *      has_more: bool,
 *      next_cursor: string|null
 * }
 *
 * @psalm-immutable
 */
final readonly class TemplateList
{
    /** @param list<Template> $templates */
    private function __construct(
        public array $templates,
        public bool $hasMore,
        public string|null $nextCursor,
    ) {
    }

    /** @param TemplateListJson $array */
    public static function fromArray(array $array): self
    {
        $templates = array_map(
            fn(array $template): Template => Template::fromArray($template),
            $array["templates"],
        );

        return new self($templates, $array["has_more"], $array["next_cursor"]);
    }

    public function defaultTemplate(): Template|null
    {
        foreach ($this->templates as $template) {
            if ($template->isDefault) {
                return $template;
            }
        }

        return null;
    }
}
