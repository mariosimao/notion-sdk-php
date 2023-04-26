<?php

namespace Notion\Blocks;

use Notion\Common\Color;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type NumberedListItemJson = array{
 *      numbered_list_item: array{
 *          rich_text: list<RichTextJson>,
 *          color?: string,
 *          children?: list<BlockMetadataJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class NumberedListItem implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param \Notion\Blocks\BlockInterface[] $children
     */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $text,
        public readonly Color $color,
        public readonly array $children,
    ) {
        $metadata->checkType(BlockType::NumberedListItem);
    }

    public static function create(): self
    {
        $block = BlockMetadata::create(BlockType::NumberedListItem);

        return new self($block, [], Color::Default, []);
    }

    public static function fromString(string $content): self
    {
        $block = BlockMetadata::create(BlockType::NumberedListItem);
        $text = [ RichText::fromString($content) ];

        return new self($block, $text, Color::Default, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var NumberedListItemJson $array */
        $item = $array["numbered_list_item"];

        $text = array_map(fn($t) => RichText::fromArray($t), $item["rich_text"]);

        $color = Color::tryFrom($item["color"] ?? "") ?? Color::Default;

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $item["children"] ?? []);

        return new self($block, $text, $color, $children);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["numbered_list_item"] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "color"     => $this->color->value,
            "children"  => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
        ];

        return $array;
    }

    public function toString(): string
    {
        return RichText::multipleToString(...$this->text);
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function changeText(RichText ...$text): self
    {
        return new self($this->metadata->update(), $text, $this->color, $this->children);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata->update(), $texts, $this->color, $this->children);
    }

    public function addChild(BlockInterface $child): self
    {
        $children = $this->children;
        $children[] = $child;

        return new self(
            $this->metadata->updateHasChildren(true),
            $this->text,
            $this->color,
            $children,
        );
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            $this->text,
            $this->color,
            $children,
        );
    }

    public function changeColor(Color $color): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $color,
            $this->children,
        );
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->text,
            $this->color,
            $this->children,
        );
    }
}
