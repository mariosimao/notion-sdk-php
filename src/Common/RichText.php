<?php

namespace Notion\Common;

/**
 * @psalm-import-type AnnotationsJson from Annotations
 * @psalm-import-type TextJson from Text
 * @psalm-import-type MentionJson from Mention
 * @psalm-import-type EquationJson from Equation
 *
 * @psalm-type RichTextJson = array{
 *      plain_text: string,
 *      href: string|null,
 *      annotations: AnnotationsJson,
 *      type: "text"|"mention"|"equation",
 *      text?: TextJson,
 *      mention?: MentionJson,
 *      equation?: EquationJson,
 * }
 *
 * @psalm-immutable
 */
class RichText
{
    private function __construct(
        public readonly string $plainText,
        public readonly string|null $href,
        public readonly Annotations $annotations,
        public readonly RichTextType $type,
        public readonly Text|null $text,
        public readonly Mention|null $mention,
        public readonly Equation|null $equation,
    ) {
    }

    /** @psalm-mutation-free */
    public static function createText(string $content): self
    {
        $text = Text::create($content);

        return self::createFromText($text);
    }

    public static function createLink(string $content, string $url): self
    {
        $text = Text::create($content)->changeUrl($url);

        return self::createFromText($text);
    }

    /** @psalm-mutation-free */
    public static function createFromText(Text $text): self
    {
        $annotations = Annotations::create();

        return new self(
            $text->content,
            $text->url,
            $annotations,
            RichTextType::Text,
            $text,
            null,
            null
        );
    }

    public static function createEquation(string $expression): self
    {
        $equation = Equation::create($expression);

        return self::createFromEquation($equation);
    }

    public static function createFromEquation(Equation $equation): self
    {
        $annotations = Annotations::create();

        return new self(
            $equation->expression,
            null,
            $annotations,
            RichTextType::Equation,
            null,
            null,
            $equation
        );
    }

    public static function newLine(): self
    {
        $text = Text::create("\n");

        return self::createFromText($text);
    }

    /**
     * @psalm-param RichTextJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["plain_text"],
            isset($array["href"]) ? $array["href"] : null,
            Annotations::fromArray($array["annotations"]),
            RichTextType::from($array["type"]),
            array_key_exists("text", $array) ? Text::fromArray($array["text"]) : null,
            array_key_exists("mention", $array) ? Mention::fromArray($array["mention"]) : null,
            array_key_exists("equation", $array) ? Equation::fromArray($array["equation"]) : null,
        );
    }

    public function toString(): string
    {
        return $this->plainText;
    }

    public function toArray(): array
    {
        $array = [
            "plain_text"  => $this->plainText,
            "href"        => $this->href,
            "annotations" => $this->annotations->toArray(),
            "type"        => $this->type->value,
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

    /**
     * @psalm-assert-if-true Text $this->text
     */
    public function isText(): bool
    {
        return $this->type === RichTextType::Text;
    }

    /**
     * @psalm-assert-if-true Mention $this->mention
     */
    public function isMention(): bool
    {
        return $this->type === RichTextType::Mention;
    }

    /**
     * @psalm-assert-if-true Equation $this->equation
     */
    public function isEquation(): bool
    {
        return $this->type === RichTextType::Equation;
    }

    public function changeHref(string $href): self
    {
        return new self(
            $this->plainText,
            $href,
            $this->annotations,
            $this->type,
            $this->text,
            $this->mention,
            $this->equation,
        );
    }

    public function changeAnnotations(Annotations $annotations): self
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

    public function bold(bool $bold = true): self
    {
        $annotations = $this->annotations->bold($bold);

        return $this->changeAnnotations($annotations);
    }

    public function italic(bool $italic = true): self
    {
        $annotations = $this->annotations->italic($italic);

        return $this->changeAnnotations($annotations);
    }

    public function strikeThrough(bool $strikeThrough = true): self
    {
        $annotations = $this->annotations->strikeThrough($strikeThrough);

        return $this->changeAnnotations($annotations);
    }

    public function underline(bool $underline = true): self
    {
        $annotations = $this->annotations->underline($underline);

        return $this->changeAnnotations($annotations);
    }

    public function code(bool $code = true): self
    {
        $annotations = $this->annotations->code($code);

        return $this->changeAnnotations($annotations);
    }

    public function color(Color $color): self
    {
        $annotations = $this->annotations->changeColor($color);

        return $this->changeAnnotations($annotations);
    }

    /** @psalm-mutation-free */
    public static function multipleToString(self ...$richText): string
    {
        $string = "";
        foreach ($richText as $singleRichText) {
            $string = $string . $singleRichText->toString();
        }

        return $string;
    }
}
