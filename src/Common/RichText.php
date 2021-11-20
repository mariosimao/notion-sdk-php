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

    /** @psalm-mutation-free */
    public static function createText(string $content): self
    {
        $annotations = Annotations::create();
        $text = Text::create($content);

        return new self($content, null, $annotations, "text", $text, null, null);
    }

    public static function createEquation(string $expression): self
    {
        $annotations = Annotations::create();
        $equation = Equation::create($expression);

        return new self(
            $expression,
            null,
            $annotations,
            "equation",
            null,
            null,
            $equation,
        );
    }

    /**
     * @param RichTextJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["plain_text"],
            isset($array["href"]) ? $array["href"] : null,
            Annotations::fromArray($array["annotations"]),
            $array["type"],
            array_key_exists("text", $array) ? Text::fromArray($array["text"]) : null,
            array_key_exists("mention", $array) ? Mention::fromArray($array["mention"]) : null,
            array_key_exists("equation", $array) ? Equation::fromArray($array["equation"]) : null,
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

    /**
     * @psalm-assert-if-true Text $this->text
     * @psalm-assert-if-true Text $this->text()
     */
    public function isText(): bool
    {
        return $this->type === "text";
    }

    /**
     * @psalm-assert-if-true Mention $this->mention
     * @psalm-assert-if-true Mention $this->mention()
     */
    public function isMention(): bool
    {
        return $this->type === "mention";
    }

    /**
     * @psalm-assert-if-true Equation $this->equation
     * @psalm-assert-if-true Equation $this->equation()
     */
    public function isEquation(): bool
    {
        return $this->type === "equation";
    }

    public function withHref(string $href): self
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

    public function bold(bool $bold = true): self
    {
        $annotations = $this->annotations->bold($bold);

        return $this->withAnnotations($annotations);
    }

    public function italic(bool $italic = true): self
    {
        $annotations = $this->annotations->italic($italic);

        return $this->withAnnotations($annotations);
    }

    public function strikeThrough(bool $strikeThrough = true): self
    {
        $annotations = $this->annotations->strikeThrough($strikeThrough);

        return $this->withAnnotations($annotations);
    }

    public function underline(bool $underline = true): self
    {
        $annotations = $this->annotations->underline($underline);

        return $this->withAnnotations($annotations);
    }

    public function code(bool $code = true): self
    {
        $annotations = $this->annotations->code($code);

        return $this->withAnnotations($annotations);
    }

    /** @param Annotations::COLOR_* $color */
    public function color(string $color): self
    {
        $annotations = $this->annotations->withColor($color);

        return $this->withAnnotations($annotations);
    }
}
