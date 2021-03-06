<?php

namespace Notion\Blocks;

use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\RichText;
use Notion\NotionException;

/**
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type Heading2Json = array{
 *      heading_2: array{
 *          rich_text: list<RichTextJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Heading2 implements BlockInterface
{
    private const TYPE = Block::TYPE_HEADING_2;

    private Block $block;

    /** @var list<RichText> */
    private array $text;

    /**
     * @param list<RichText> $text
     */
    private function __construct(Block $block, array $text)
    {
        if (!$block->isHeading2()) {
            throw new BlockTypeException(self::TYPE);
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

        /** @psalm-var Heading2Json $array */
        $heading = $array[self::TYPE];

        $text = array_map(fn($t) => RichText::fromArray($t), $heading["rich_text"]);

        return new self($block, $text);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
        ];

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            self::TYPE => [
                "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            ],
            "archived" => $this->block()->archived(),
        ];
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

    public function changeChildren(array $children): self
    {
        throw new NotionException(
            "This block does not support children.",
            "no_children_support",
        );
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->block->archive(),
            $this->text,
        );
    }
}
