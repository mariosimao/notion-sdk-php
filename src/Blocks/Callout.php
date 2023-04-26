<?php

namespace Notion\Blocks;

use Notion\Common\Color;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type EmojiJson from \Notion\Common\Emoji
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type CalloutJson = array{
 *      callout: array{
 *          rich_text: list<RichTextJson>,
 *          color?: string,
 *          children?: list<BlockMetadataJson>,
 *          icon: EmojiJson|FileJson,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Callout implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param BlockInterface[] $children
     */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $text,
        public readonly Icon $icon,
        public readonly Color $color,
        public readonly array $children,
    ) {
        $metadata->checkType(BlockType::Callout);
    }

    public static function create(): self
    {
        $metadata = BlockMetadata::create(BlockType::Callout);
        $icon = Icon::fromEmoji(Emoji::fromString("⭐"));

        return new self($metadata, [], $icon, Color::Default, []);
    }

    public static function fromString(string $emoji, string $content): self
    {
        $metadata = BlockMetadata::create(BlockType::Callout);
        $text = [ RichText::fromString($content) ];
        $icon = Icon::fromEmoji(Emoji::fromString($emoji));

        return new self($metadata, $text, $icon, Color::Default, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var CalloutJson $array */
        $callout = $array["callout"];

        $text = array_map(fn($t) => RichText::fromArray($t), $callout["rich_text"]);

        $iconArray = $callout["icon"];
        if ($iconArray["type"] === "emoji") {
            /** @psalm-var EmojiJson $iconArray */
            $emoji = Emoji::fromArray($iconArray);
            $icon = Icon::fromEmoji($emoji);
        } else {
            /** @psalm-var FileJson $iconArray */
            $file = File::fromArray($iconArray);
            $icon = Icon::fromFile($file);
        }

        $color = Color::tryFrom($callout["color"] ?? "") ?? Color::Default;

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $callout["children"] ?? []);

        return new self($metadata, $text, $icon, $color, $children);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["callout"] = [
            "rich_text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "icon"     => $this->icon->toArray(),
            "color"    => $this->color->value,
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
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
        return new self($this->metadata, $text, $this->icon, $this->color, $this->children);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->icon, $this->color, $this->children);
    }

    public function changeIcon(Emoji|File|Icon $icon): self
    {
        if ($icon instanceof Emoji) {
            $icon = Icon::fromEmoji($icon);
        }

        if ($icon instanceof File) {
            $icon = Icon::fromFile($icon);
        }

        return new self($this->metadata, $this->text, $icon, $this->color, $this->children);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            $this->text,
            $this->icon,
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
            $this->icon,
            $this->color,
            $children,
        );
    }

    public function changeColor(Color $color): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $this->icon,
            $color,
            $this->children,
        );
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->text,
            $this->icon,
            $this->color,
            $this->children,
        );
    }
}
