<?php

namespace Notion\Common;

class Annotations
{
    private const ALLOWED_COLORS = [
        "default", "gray", "brown", "orange", "yellow", "green", "blue",
        "purple", "pink", "red", "gray_background", "brown_background",
        "orange_background", "yellow_background", "green_background",
        "blue_background", "purple_background", "pink_background",
        "red_background",
    ];

    private bool $bold;
    private bool $italic;
    private bool $strikeThrough;
    private bool $underline;
    private bool $code;
    private string $color;

    private function __construct(
        bool $bold,
        bool $italic,
        bool $strikeThrough,
        bool $underline,
        bool $code,
        string $color,
    ) {
        $this->checkColor($color);

        $this->bold = $bold;
        $this->italic = $italic;
        $this->strikeThrough = $strikeThrough;
        $this->underline = $underline;
        $this->code = $code;
        $this->color = $color;
    }

    public static function create(): self
    {
        return new self(false, false, false, false, false, "default");
    }

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

    public function bold($bold = true): self
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

    public function italic($italic = true): self
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

    public function strikeThrough($strikeThrough = true): self
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

    public function underline($underline = true): self
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

    public function code($code = true): self
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

    private function checkColor(string $color): void
    {
        if (!in_array($color, self::ALLOWED_COLORS)) {
            throw new \Exception("Invalid color: '{$color}'.");
        }
    }
}
