<?php

namespace Notion\Blocks;

use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type ToggleJson = array{
 *      toggle: array{
 *          text: list<RichTextJson>,
 *          children?: list<BlockJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Toggle implements BlockInterface
{
    private const TYPE = Block::TYPE_TOGGLE;

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
        if (!$block->isToggle()) {
            throw new BlockTypeException(self::TYPE);
        }

        $this->block = $block;
        $this->text = $text;
        $this->children = $children;
    }

    public static function create(): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, [], []);
    }

    public static function fromString(string $content): self
    {
        $block = Block::create(self::TYPE);
        $text = [ RichText::createText($content) ];

        return new self($block, $text, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var ToggleJson $array */
        $toggle = $array[self::TYPE];

        $text = array_map(fn($t) => RichText::fromArray($t), $toggle["rich_text"]);

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $toggle["children"] ?? []);

        return new self($block, $text, $children);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "rich_text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
        ];

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            self::TYPE => [
                "rich_text"    => array_map(fn(RichText $t) => $t->toArray(), $this->text),
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

    /** @return list<BlockInterface> */
    public function children(): array
    {
        return $this->children;
    }

    /** @param list<RichText> $text */
    public function withText(array $text): self
    {
        return new self($this->block, $text, $this->children);
    }

    public function appendText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->block, $texts, $this->children);
    }

    /** @param list<BlockInterface> $children */
    public function changeChildren(array $children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->block->withHasChildren($hasChildren),
            $this->text,
            $children,
        );
    }

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

    public function archive(): BlockInterface
    {
        return new self(
            $this->block->archive(),
            $this->text,
            $this->children,
        );
    }
}
