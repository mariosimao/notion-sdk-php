<?php

namespace Notion\Blocks;

use Notion\Common\Color;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type ParagraphJson = array{
 *      paragraph: array{
 *          rich_text: list<RichTextJson>,
 *          children?: list<BlockMetadataJson>,
 *          color?: string,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Paragraph implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param BlockInterface[] $children
     */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $text,
        public readonly array $children,
        public readonly Color $color,
    ) {
        $metadata->checkType(BlockType::Paragraph);
    }

    public static function create(): self
    {
        $block = BlockMetadata::create(BlockType::Paragraph);

        return new self($block, [], [], Color::Default);
    }

    public static function fromString(string $content): self
    {
        $block = BlockMetadata::create(BlockType::Paragraph);
        $text = [ RichText::fromString($content) ];

        return new self($block, $text, [], Color::Default);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var ParagraphJson $array */
        $paragraph = $array["paragraph"];

        $text = array_map(fn($t) => RichText::fromArray($t), $paragraph["rich_text"]);

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $paragraph["children"] ?? []);

        $color = Color::tryFrom($paragraph["color"] ?? "") ?? Color::Default;

        return new self($block, $text, $children, $color);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["paragraph"] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "children"  => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
            "color"     => $this->color->value,
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
        return new self($this->metadata, $text, $this->children, $this->color);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->children, $this->color);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            $this->text,
            $children,
            $this->color,
        );
    }

    public function addChild(BlockInterface $child): self
    {
        $children = $this->children;
        $children[] = $child;

        return new self(
            $this->metadata->updateHasChildren(true),
            $this->text,
            $children,
            $this->color,
        );
    }

    public function changeColor(Color $color): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $this->children,
            $color,
        );
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->text,
            $this->children,
            $this->color,
        );
    }
}
