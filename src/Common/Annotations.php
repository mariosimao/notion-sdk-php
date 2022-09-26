<?php

namespace Notion\Common;

/** @psalm-type AnnotationsJson = array{
 *      bold: bool,
 *      italic: bool,
 *      strikethrough: bool,
 *      underline: bool,
 *      code: bool,
 *      color: string,
 * }
 *
 * @psalm-immutable
 */
class Annotations
{
    private function __construct(
        public readonly bool $isBold,
        public readonly bool $isItalic,
        public readonly bool $isStrikeThrough,
        public readonly bool $isUnderline,
        public readonly bool $isCode,
        public readonly Color $color,
    ) {
    }

    /** @psalm-mutation-free */
    public static function create(): self
    {
        return new self(false, false, false, false, false, Color::Default);
    }

    /**
     * @param AnnotationsJson $array
     *
     * @internal
    */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["bold"],
            $array["italic"],
            $array["strikethrough"],
            $array["underline"],
            $array["code"],
            Color::from($array["color"]),
        );
    }

    public function toArray(): array
    {
        return [
            "bold"          => $this->isBold,
            "italic"        => $this->isItalic,
            "strikethrough" => $this->isStrikeThrough,
            "underline"     => $this->isUnderline,
            "code"          => $this->isCode,
            "color"         => $this->color->value,
        ];
    }

    public function bold(bool $bold = true): self
    {
        return new self(
            $bold,
            $this->isItalic,
            $this->isStrikeThrough,
            $this->isUnderline,
            $this->isCode,
            $this->color,
        );
    }

    public function italic(bool $italic = true): self
    {
        return new self(
            $this->isBold,
            $italic,
            $this->isStrikeThrough,
            $this->isUnderline,
            $this->isCode,
            $this->color,
        );
    }

    public function strikeThrough(bool $strikeThrough = true): self
    {
        return new self(
            $this->isBold,
            $this->isItalic,
            $strikeThrough,
            $this->isUnderline,
            $this->isCode,
            $this->color,
        );
    }

    public function underline(bool $underline = true): self
    {
        return new self(
            $this->isBold,
            $this->isItalic,
            $this->isStrikeThrough,
            $underline,
            $this->isCode,
            $this->color,
        );
    }

    public function code(bool $code = true): self
    {
        return new self(
            $this->isBold,
            $this->isItalic,
            $this->isStrikeThrough,
            $this->isUnderline,
            $code,
            $this->color,
        );
    }

    public function changeColor(Color $color): self
    {
        return new self(
            $this->isBold,
            $this->isItalic,
            $this->isStrikeThrough,
            $this->isUnderline,
            $this->isCode,
            $color,
        );
    }
}
