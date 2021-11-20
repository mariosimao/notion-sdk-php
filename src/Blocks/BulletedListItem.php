<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * Bulleted list item
 *
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type BulletedListItemJson = array{
 *      bulleted_list_item: array{
 *          text: list<RichTextJson>,
 *          children: list<BlockJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class BulletedListItem implements BlockInterface
{
    private const TYPE = Block::TYPE_BULLETED_LIST_ITEM;

    private Block $block;

    /** @var list<RichText> */

    private array $text;

    /** @var list<\Notion\Blocks\BlockInterface> */
    private array $children;

    /**
     * @param list<RichText> $text
     * @param list<\Notion\Blocks\BlockInterface> $children
     */
    private function __construct(
        Block $block,
        array $text,
        array $children,
    ) {
        if (!$block->isBulletedListItem()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->text = $text;
        $this->children = $children;
    }

    /**
     * Create empty bulleted list item
     */
    public static function create(): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, [], []);
    }

    /**
     * Create bulleted list item from a string
     */
    public static function fromString(string $content): self
    {
        $block = Block::create(self::TYPE);
        $text = [ RichText::createText($content) ];

        return new self($block, $text, []);
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var BulletedListItemJson $array */
        $item = $array[self::TYPE];

        $text = array_map(fn($t) => RichText::fromArray($t), $item["text"]);

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $item["children"]);

        return new self($block, $text, $children);
    }

    /** @internal */
    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
        ];

        return $array;
    }

    /** Get item content as string */
    public function toString(): string
    {
        $string = "";
        foreach ($this->text as $richText) {
            $string = $string . $richText->plainText();
        }

        return $string;
    }

    /** Get block common object */
    public function block(): Block
    {
        return $this->block;
    }

    /**
     *  Get list item text
     *
     * @return list<RichText>
     */
    public function text(): array
    {
        return $this->text;
    }

    /**
     * Get children blocks
     *
     * @return BlockInterface[]
     */
    public function children(): array
    {
        return $this->children;
    }

    /**
     * Change list item text
     */
    /** @param list<RichText> $text */
    public function withText(array $text): self
    {
        return new self($this->block, $text, $this->children);
    }

    /**
     * Append text to list item
     */
    public function appendText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->block, $texts, $this->children);
    }

    /** Change list item children blocks */
    /** @param list<BlockInterface> $children */
    public function withChildren(array $children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->block->withHasChildren($hasChildren),
            $this->text,
            $children,
        );
    }

    /** Append child block */
    public function appendChild(BlockInterface $child): self
    {
        $children = $this->children;
        $children[] = $child;

        return new self(
            $this->block->withHasChildren(true),
            $this->text,
            $children,
        );
    }
}
