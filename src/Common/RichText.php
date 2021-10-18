<?php

namespace Notion\Common;

class RichText
{
    private const ALLOWED_TYPES = [ "text", "mention", "equation" ];

    private string $plainText;
    private string|null $href;
    private Annotations $annotations;
    private string $type;
    private Text|null $text;
    private Mention|null $mention;
    private Equation|null $equation;

    private function __construct(
        string $plainText,
        string|null $href,
        Annotations $annotations,
        string $type,
        Text|null $text,
        Mention|null $mention,
        Equation|null $equation,
    ) {
        $this->plainText = $plainText;
        $this->href = $href;
        $this->annotations = $annotations;
        $this->type = $type;
        $this->text = $text;
        $this->mention = $mention;
        $this->equation = $equation;
    }

    public static function createText($content): self
    {
        $annotations = Annotations::create();
        $text = Text::create($content);

        return new self($content, null, $annotations, "text", $text, null, null);
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array["plain_text"],
            isset($array["href"]) ? $array["href"] : null,
            Annotations::fromArray($array["annotations"]),
            $array["type"],
            $array["type"] === "text" ? Text::fromArray($array["text"]) : null,
            $array["type"] === "mention" ? Mention::fromArray($array["mention"]) : null,
            $array["type"] === "equation" ? Equation::fromArray($array["equation"]) : null,
        );
    }

    public function toArray(): array
    {
        $array = [
            "plain_text"  => $this->plainText,
            "href"        => $this->href,
            "annotations" => $this->annotations->toArray(),
            "type"        => $this->type,
        ];

        if ($this->isText()) {
            $array["text"] = $this->text->toArray();
        }

        if ($this->isMention()) {
            $array["mention"] = $this->mention->toArray();
        }

        if ($this->isEquation()) {
            $array["equation"] = $this->equation->toArray();
        }

        return $array;
    }

    public function plainText(): string
    {
        return $this->plainText;
    }

    public function href(): string|null
    {
        return $this->href;
    }

    public function annotations(): Annotations
    {
        return $this->annotations;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function text(): Text|null
    {
        return $this->text;
    }

    public function mention(): Mention|null
    {
        return $this->mention;
    }

    public function equation(): Equation|null
    {
        return $this->equation;
    }

    public function isText(): bool
    {
        return $this->type === "text";
    }

    public function isMention(): bool
    {
        return $this->type === "mention";
    }

    public function isEquation(): bool
    {
        return $this->type === "equation";
    }

    public function withAnnotations(Annotations $annotations): self
    {
        return new self(
            $this->plainText,
            $this->href,
            $annotations,
            $this->type,
            $this->text,
            $this->mention,
            $this->equation,
        );
    }
}
