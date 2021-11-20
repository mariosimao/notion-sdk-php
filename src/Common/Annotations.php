<?php

namespace Notion\Common;

/** @psalm-type AnnotationsJson = array{
 *      bold: bool,
 *      italic: bool,
 *      strikethrough: bool,
 *      underline: bool,
 *      code: bool,
 *      color: self::COLOR_*,
 * }
 *
 * @psalm-immutable
 */
class Annotations
{
    public const COLOR_DEFAULT = "default";
    public const COLOR_GRAY = "gray";
    public const COLOR_BROWN = "brown";
    public const COLOR_ORANGE = "orange";
    public const COLOR_YELLOW = "yellow";
    public const COLOR_GREEN = "green";
    public const COLOR_BLUE = "blue";
    public const COLOR_PURPLE = "purple";
    public const COLOR_PINK = "pink";
    public const COLOR_RED = "red";
    public const COLOR_GRAY_BACKGROUND = "gray_background";
    public const COLOR_BROWN_BACKGROUND = "brown_background";
    public const COLOR_ORANGE_BACKGROUND = "orange_background";
    public const COLOR_YELLOW_BACKGROUND = "yellow_background";
    public const COLOR_GREEN_BACKGROUND = "green_background";
    public const COLOR_BLUE_BACKGROUND = "blue_background";
    public const COLOR_PURPLE_BACKGROUND = "purple_background";
    public const COLOR_PINK_BACKGROUND = "pink_background";
    public const COLOR_RED_BACKGROUND = "red_background";

    private bool $bold;
    private bool $italic;
    private bool $strikeThrough;
    private bool $underline;
    private bool $code;
    /** @var self::COLOR_* */
    private string $color;

    /** @param self::COLOR_* $color */
    private function __construct(
        bool $bold,
        bool $italic,
        bool $strikeThrough,
        bool $underline,
        bool $code,
        string $color,
    ) {
        $this->bold = $bold;
        $this->italic = $italic;
        $this->strikeThrough = $strikeThrough;
        $this->underline = $underline;
        $this->code = $code;
        $this->color = $color;
    }

    /** @psalm-mutation-free */
    public static function create(): self
    {
        return new self(false, false, false, false, false, "default");
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
            $array["color"],
        );
    }

    public function toArray(): array
    {
        return [
            "bold"          => $this->bold,
            "italic"        => $this->italic,
            "strikethrough" => $this->strikeThrough,
            "underline"     => $this->underline,
            "code"          => $this->code,
            "color"         => $this->color,
        ];
    }

    public function isBold(): bool
    {
        return $this->bold;
    }

    public function isItalic(): bool
    {
        return $this->italic;
    }

    public function isStrikeThrough(): bool
    {
        return $this->strikeThrough;
    }

    public function isUnderline(): bool
    {
        return $this->underline;
    }

    public function isCode(): bool
    {
        return $this->code;
    }

    public function color(): string
    {
        return $this->color;
    }

    public function bold(bool $bold = true): self
    {
        return new self(
            $bold,
            $this->italic,
            $this->strikeThrough,
            $this->underline,
            $this->code,
            $this->color,
        );
    }

    public function italic(bool $italic = true): self
    {
        return new self(
            $this->bold,
            $italic,
            $this->strikeThrough,
            $this->underline,
            $this->code,
            $this->color,
        );
    }

    public function strikeThrough(bool $strikeThrough = true): self
    {
        return new self(
            $this->bold,
            $this->italic,
            $strikeThrough,
            $this->underline,
            $this->code,
            $this->color,
        );
    }

    public function underline(bool $underline = true): self
    {
        return new self(
            $this->bold,
            $this->italic,
            $this->strikeThrough,
            $underline,
            $this->code,
            $this->color,
        );
    }

    public function code(bool $code = true): self
    {
        return new self(
            $this->bold,
            $this->italic,
            $this->strikeThrough,
            $this->underline,
            $code,
            $this->color,
        );
    }

    /** @param self::COLOR_* $color */
    public function withColor(string $color): self
    {
        return new self(
            $this->bold,
            $this->italic,
            $this->strikeThrough,
            $this->underline,
            $this->code,
            $color,
        );
    }
}
