<?php

namespace Notion\Common;

/**
 * @psalm-type LinkMentionJson = array{
 *      href: string,
 *      title?: string,
 *      description?: string,
 *      link_author?: string,
 *      link_provider?: string,
 *      thumbnail_url?: string,
 *      icon_url?: string,
 *      iframe_url?: string,
 *      height?: int,
 *      padding?: int,
 *      padding_top?: int,
 * }
 *
 * @psalm-immutable
 */
final readonly class LinkMention
{
    private function __construct(
        public string $href,
        public string|null $title = null,
        public string|null $description = null,
        public string|null $linkAuthor = null,
        public string|null $linkProvider = null,
        public string|null $thumbnailUrl = null,
        public string|null $iconUrl = null,
        public string|null $iframeUrl = null,
        public int|null $height = null,
        public int|null $padding = null,
        public int|null $paddingTop = null,
    ) {
    }

    public static function create(string $href): self
    {
        return new self($href);
    }

    /**
     * @psalm-param LinkMentionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["href"],
            $array["title"] ?? null,
            $array["description"] ?? null,
            $array["link_author"] ?? null,
            $array["link_provider"] ?? null,
            $array["thumbnail_url"] ?? null,
            $array["icon_url"] ?? null,
            $array["iframe_url"] ?? null,
            $array["height"] ?? null,
            $array["padding"] ?? null,
            $array["padding_top"] ?? null,
        );
    }

    /** @return LinkMentionJson */
    public function toArray(): array
    {
        $array = [ "href" => $this->href ];

        if ($this->title !== null) {
            $array["title"] = $this->title;
        }
        if ($this->description !== null) {
            $array["description"] = $this->description;
        }
        if ($this->linkAuthor !== null) {
            $array["link_author"] = $this->linkAuthor;
        }
        if ($this->linkProvider !== null) {
            $array["link_provider"] = $this->linkProvider;
        }
        if ($this->thumbnailUrl !== null) {
            $array["thumbnail_url"] = $this->thumbnailUrl;
        }
        if ($this->iconUrl !== null) {
            $array["icon_url"] = $this->iconUrl;
        }
        if ($this->iframeUrl !== null) {
            $array["iframe_url"] = $this->iframeUrl;
        }
        if ($this->height !== null) {
            $array["height"] = $this->height;
        }
        if ($this->padding !== null) {
            $array["padding"] = $this->padding;
        }
        if ($this->paddingTop !== null) {
            $array["padding_top"] = $this->paddingTop;
        }

        return $array;
    }
}
