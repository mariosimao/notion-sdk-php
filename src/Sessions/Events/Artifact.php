<?php

namespace Notion\Sessions\Events;

/**
 * Something the agent produced during a turn, like a page.
 *
 * @psalm-type ArtifactJson = array{
 *      type: "page"|"html_artifact",
 *      url: string,
 *      title?: string,
 *      page_url?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Artifact
{
    private function __construct(
        public ArtifactType $type,
        public string $url,
        public string|null $title,
        public string|null $pageUrl,
    ) {
    }

    /**
     * @param ArtifactJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            ArtifactType::from($array["type"]),
            $array["url"],
            $array["title"] ?? null,
            $array["page_url"] ?? null,
        );
    }

    /** @return ArtifactJson */
    public function toArray(): array
    {
        $array = [
            "type" => $this->type->value,
            "url" => $this->url,
        ];
        if ($this->title !== null) {
            $array["title"] = $this->title;
        }
        if ($this->pageUrl !== null) {
            $array["page_url"] = $this->pageUrl;
        }

        return $array;
    }
}
