<?php

namespace Notion\Blocks;

use Notion\Common\Color;
use Notion\Exceptions\BlockException;
use Notion\Common\RichText;
use Notion\Exceptions\HeadingException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type Heading1Json = array{
 *      heading_1: array{
 *          rich_text: RichTextJson[],
 *          is_toggleable: bool,
 *          color?: string,
 *          children?: BlockMetadataJson[]
 *      },
 * }
 *
 * @psalm-immutable
 */
class Heading1 implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param BlockInterface[]|null $children
     */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $text,
        public readonly bool $isToggleable,
        public readonly Color $color,
        public readonly array|null $children,
    ) {
        $metadata->checkType(BlockType::Heading1);
    }

    public static function fromText(RichText ...$text): self
    {
        $block = BlockMetadata::create(BlockType::Heading1);

        return new self($block, $text, false, Color::Default, []);
    }

    public static function fromString(string $content): self
    {
        $block = BlockMetadata::create(BlockType::Heading1);
        $text = [ RichText::fromString($content) ];

        return new self($block, $text, false, Color::Default, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var Heading1Json $array */
        $heading = $array["heading_1"];

        $text = array_map(fn($t) => RichText::fromArray($t), $heading["rich_text"]);

        $isToggleable = $heading["is_toggleable"];

        $color = Color::tryFrom($heading["color"] ?? "") ?? Color::Default;

        $children = null;
        if ($isToggleable) {
            $children = array_map(fn($b) => BlockFactory::fromArray($b), $heading["children"] ?? []);
        }

        return new self($block, $text, $isToggleable, $color, $children);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["heading_1"] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "is_toggleable" => $this->isToggleable,
            "color" => $this->color->value,
            "children" => array_map(fn($b) => $b->toArray(), $this->children ?? [])
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

    public function changeText(RichText ...$text): self
    {
        return new self($this->metadata, $text, $this->isToggleable, $this->color, $this->children);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->isToggleable, $this->color, $this->children);
    }

    public function toggllify(): self
    {
        return new self($this->metadata, $this->text, true, $this->color, []);
    }

    public function untogglify(): self
    {
        if (!empty($this->children)) {
            throw HeadingException::untogglifyWithChildren();
        }

        return new self($this->metadata, $this->text, false, $this->color, null);
    }

    public function changeColor(Color $color): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $this->isToggleable,
            $color,
            $this->children,
        );
    }

    public function addChild(BlockInterface $child): self
    {
        if (!$this->isToggleable) {
            throw BlockException::noChindrenSupport();
        }

        $children = $this->children ? [...$this->children, $child] : [$child];
        return new self(
            $this->metadata,
            $this->text,
            $this->isToggleable,
            $this->color,
            $children,
        );
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        if (!$this->isToggleable) {
            throw BlockException::noChindrenSupport();
        }

        return new self($this->metadata, $this->text, $this->isToggleable, $this->color, $children);
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->text,
            $this->isToggleable,
            $this->color,
            $this->children,
        );
    }
}
