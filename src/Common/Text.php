<?php

namespace Notion\Common;

/**
 * @psalm-type TextJson = array{ content: string, link?: array{ url: string } }
 *
 * @psalm-immutable
 */
class Text
{
    private function __construct(
        public readonly string $content,
        public readonly string|null $url,
    ) {
    }

    /** @psalm-mutation-free */
    public static function fromString(string $content): self
    {
        return new self($content, null);
    }

    /**
     * @param TextJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $url = isset($array["link"]) ? $array["link"]["url"] : null;

        return new self($array["content"], $url);
    }

    public function toArray(): array
    {
        $array = [ "content" => $this->content ];
        if ($this->url !== null) {
            $array["link"] = [ "url" => $this->url ];
        }

        return $array;
    }

    public function changeContent(string $content): self
    {
        return new self($content, $this->url);
    }

    public function changeUrl(string $url): self
    {
        return new self($this->content, $url);
    }

    public function removeUrl(): self
    {
        return new self($this->content, null);
    }
}
