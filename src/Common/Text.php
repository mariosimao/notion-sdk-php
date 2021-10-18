<?php

namespace Notion\Common;

class Text
{
    private string $content;
    private string|null $url;

    private function __construct(string $content, string|null $url)
    {
        $this->content = $content;
        $this->url = $url;
    }

    public static function create(string $content): self
    {
        return new self($content, null);
    }

    public static function fromArray(array $array): self
    {
        $url = isset($array["link"]) ? $array["link"]["url"] : null;

        return new self($array["content"], $url);
    }

    public function toArray(): array
    {
        $link = ($this->url !== null) ? [ "url" => $this->url ] : null;

        return [
            "content" => $this->content,
            "link"    => $link,
        ];
    }

    public function content(): string
    {
        return $this->content;
    }

    public function url(): string|null
    {
        return $this->url;
    }

    public function withContent(string $content): self
    {
        return new self($content, $this->url);
    }

    public function withUrl(string $url): self
    {
        return new self($this->content, $url);
    }

    public function withoutUrl(): self
    {
        return new self($this->content, null);
    }
}
