<?php

namespace Notion\Blocks;

use Notion\Common\Color;
use Notion\Exceptions\BlockException;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type QuoteJson = array{
 *      quote: array{
 *          rich_text: list<RichTextJson>,
 *          color?: string,
 *          children: list<BlockMetadataJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Quote implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param BlockInterface[] $children
     */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $text,
        public readonly Color $color,
        public readonly array $children,
    ) {
        $metadata->checkType(BlockType::Quote);
    }

    public static function create(): self
    {
        $block = BlockMetadata::create(BlockType::Quote);

        return new self($block, [], Color::Default, []);
    }

    public static function fromString(string $content): self
    {
        $block = BlockMetadata::create(BlockType::Quote);
        $text = [ RichText::fromString($content) ];

        return new self($block, $text, Color::Default, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var QuoteJson $array */
        $quote = $array["quote"];

        $text = array_map(fn($t) => RichText::fromArray($t), $quote["rich_text"]);

        $color = Color::tryFrom($quote["color"] ?? "") ?? Color::Default;

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $quote["children"] ?? []);

        return new self($block, $text, $color, $children);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["quote"] = [
            "rich_text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "color"    => $this->color->value,
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
        ];

        return $array;
    }

    public function toString(): string
    {
        $string = "";
        foreach ($this->text as $richText) {
            $string = $string . $richText->plainText;
        }

        return $string;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    /** @param RichText[] $text */
    public function changeText(array $text): self
    {
        return new self($this->metadata, $text, $this->color, $this->children);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->color, $this->children);
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
