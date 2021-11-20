<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type Heading3Json = array{
 *      heading_3: array{
 *          text: list<RichTextJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Heading3 implements BlockInterface
{
    private const TYPE = Block::TYPE_HEADING_3;

    private Block $block;

    /** @var list<RichText> */
    private array $text;

    /**
     * @param list<RichText> $text
     */
    private function __construct(Block $block, array $text)
    {
        if (!$block->isHeading3()) {
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

    public static function fromString(string $content): self
    {
        $block = Block::create(self::TYPE);
        $text = [ RichText::createText($content) ];

        return new self($block, $text);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var Heading3Json $array */
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

    /** @param list<RichText> $text */
    public function withText(array $text): self
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
