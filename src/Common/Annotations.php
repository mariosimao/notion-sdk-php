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
        if (!in_array($color, self::ALLOWED_COLORS)) {
            throw new \Exception("Invalid color: '{$color}'.");
        }

        $this->bold = $bold;
        $this->italic = $italic;
        $this->strikeThrough = $strikeThrough;
        $this->underline = $underline;
        $this->code = $code;
        $this->color = $color;
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
            "strikeThrough" => $this->strikeThrough,
            "underline"     => $this->underline,
            "code"          => $this->code,
            "color"         => $this->color,
        ];
    }

    public function bold(): bool
    {
        return $this->bold;
    }

    public function italic(): bool
    {
        return $this->italic;
    }

    public function strikeThrough(): bool
    {
        return $this->strikeThrough;
    }

    public function underline(): bool
    {
        return $this->underline;
    }

    public function code(): bool
    {
        return $this->code;
    }

    public function color(): string
    {
        return $this->color;
    }
}
