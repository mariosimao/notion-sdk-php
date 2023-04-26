<?php

namespace Notion\Blocks;

use Notion\Common\Color;
use Notion\Exceptions\BlockException;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type ToDoJson = array{
 *      to_do: array{
 *          checked: bool,
 *          rich_text: list<RichTextJson>,
 *          color?: string,
 *          children?: list<BlockMetadataJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class ToDo implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param BlockInterface[] $children
     */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $text,
        public readonly bool $checked,
        public readonly Color $color,
        public readonly array $children,
    ) {
        $metadata->checkType(BlockType::ToDo);
    }

    public static function create(): self
    {
        $block = BlockMetadata::create(BlockType::ToDo);

        return new self($block, [], false, Color::Default, []);
    }

    public static function fromString(string $content): self
    {
        $block = BlockMetadata::create(BlockType::ToDo);
        $text = [ RichText::fromString($content) ];

        return new self($block, $text, false, Color::Default, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var ToDoJson $array */
        $todo = $array["to_do"];

        $text = array_map(fn($t) => RichText::fromArray($t), $todo["rich_text"]);

        $checked = $todo["checked"];

        $color = Color::tryFrom($todo["color"] ?? "") ?? Color::Default;

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $todo["children"] ?? []);

        return new self($block, $text, $checked, $color, $children);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["to_do"] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "checked"   => $this->checked,
            "color"     => $this->color->value,
            "children"  => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
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
        return new self($this->metadata, $text, $this->checked, $this->color, $this->children);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->checked, $this->color, $this->children);
    }

    public function check(): self
    {
        return new self($this->metadata, $this->text, true, $this->color, $this->children);
    }

    public function uncheck(): self
    {
        return new self($this->metadata, $this->text, false, $this->color, $this->children);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            $this->text,
            $this->checked,
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
            $this->checked,
            $this->color,
            $children,
        );
    }

    public function changeColor(Color $color): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $this->checked,
            $color,
            $this->children,
        );
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->text,
            $this->checked,
            $this->color,
            $this->children,
        );
    }
}
