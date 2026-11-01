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
 *          list_start_index?: int,
 *          list_format?: "numbers"|"letters"|"roman",
 *          children?: list<BlockMetadataJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
final readonly class NumberedListItem implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param \Notion\Blocks\BlockInterface[] $children
     */
    private function __construct(
        private BlockMetadata $metadata,
        public array $text,
        public Color $color,
        public array $children,
        public int|null $listStartIndex,
        public NumberedListFormat|null $listFormat,
    ) {
        $metadata->checkType(BlockType::NumberedListItem);
    }

    public static function create(): self
    {
        $block = BlockMetadata::create(BlockType::NumberedListItem);

        return new self($block, [], Color::Default, [], null, null);
    }

    public static function fromString(string $content): self
    {
        $block = BlockMetadata::create(BlockType::NumberedListItem);
        $text = [ RichText::fromString($content) ];

        return new self($block, $text, Color::Default, [], null, null);
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

        $listStartIndex = $item["list_start_index"] ?? null;
        $listFormat = NumberedListFormat::tryFrom($item["list_format"] ?? "");

        return new self($block, $text, $color, $children, $listStartIndex, $listFormat);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $item = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "color"     => $this->color->value,
            "children"  => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
        ];

        if ($this->listStartIndex !== null) {
            $item["list_start_index"] = $this->listStartIndex;
        }

        if ($this->listFormat !== null) {
            $item["list_format"] = $this->listFormat->value;
        }

        $array["numbered_list_item"] = $item;

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
        return new self(
            $this->metadata->update(),
            $text,
            $this->color,
            $this->children,
            $this->listStartIndex,
            $this->listFormat,
        );
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self(
            $this->metadata->update(),
            $texts,
            $this->color,
            $this->children,
            $this->listStartIndex,
            $this->listFormat,
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
            $this->listStartIndex,
            $this->listFormat,
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
            $this->listStartIndex,
            $this->listFormat,
        );
    }

    public function changeColor(Color $color): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $color,
            $this->children,
            $this->listStartIndex,
            $this->listFormat,
        );
    }

    /** Only applies to the first item of a list. */
    public function changeListStartIndex(int|null $listStartIndex): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $this->color,
            $this->children,
            $listStartIndex,
            $this->listFormat,
        );
    }

    /** Only applies to the first item of a list. */
    public function changeListFormat(NumberedListFormat|null $listFormat): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $this->color,
            $this->children,
            $this->listStartIndex,
            $listFormat,
        );
    }

    public function delete(): BlockInterface
    {
        return new self(
            $this->metadata->delete(),
            $this->text,
            $this->color,
            $this->children,
            $this->listStartIndex,
            $this->listFormat,
        );
    }

    /**
     * @deprecated 1.17.0 Use `delete()` instead.
     * @codeCoverageIgnore
     */
    public function archive(): BlockInterface
    {
        return $this->delete();
    }
}
