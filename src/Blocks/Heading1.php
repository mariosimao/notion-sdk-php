<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

class Heading1 implements BlockInterface
{
    private const TYPE = Block::TYPE_HEADING_1;

    private Block $block;

    /** @var \Notion\Common\RichText[] */
    private array $text;

    private function __construct(Block $block, array $text) {
        if (!$block->isHeading1()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->text = $text;
    }

    public static function create(): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, []);
    }

    public static function fromString($content): self
    {
        $block = Block::create(self::TYPE);
        $text = [ RichText::createText($content) ];

        return new self($block, $text);
    }

    public static function fromArray(array $array): self
    {
        $block = Block::fromArray($array);

        $heading = $array[self::TYPE];

        $text = array_map(fn($t) => RichText::fromArray($t), $heading["text"]);

        return new self($block, $text);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
        ];

        return $array;
    }

    public function toString(): string
    {
        $string = "";
        foreach ($this->text as $richText) {
            $string = $string . $richText->plainText();
        }

        return $string;
    }

    public function block(): Block
    {
        return $this->block;
    }

    public function text(): array
    {
        return $this->text;
    }

    public function withText(RichText ...$text): self
    {
        return new self($this->block, $text);
    }

    public function appendText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->block, $texts);
    }
}
