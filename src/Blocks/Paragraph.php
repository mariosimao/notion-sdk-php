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
 * @psalm-type ParagraphJson = array{
 *      paragraph: array{
 *          rich_text: list<RichTextJson>,
 *          children?: list<BlockMetadataJson>,
 *          color?: string,
 *          icon?: EmojiJson|FileJson|null,
 *      },
 * }
 *
 * @psalm-immutable
 */
final readonly class Paragraph implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param BlockInterface[] $children
     */
    private function __construct(
        private BlockMetadata $metadata,
        public array $text,
        public array $children,
        public Color $color,
        public Icon|null $icon = null,
        private bool $iconRemoved = false,
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

        $icon = null;
        $iconArray = $paragraph["icon"] ?? null;
        if (is_array($iconArray)) {
            if ($iconArray["type"] === "emoji") {
                /** @psalm-var EmojiJson $iconArray */
                $icon = Icon::fromEmoji(Emoji::fromArray($iconArray));
            }

            if ($iconArray["type"] === "file" || $iconArray["type"] === "external") {
                /** @psalm-var FileJson $iconArray */
                $icon = Icon::fromFile(File::fromArray($iconArray));
            }
        }

        return new self($block, $text, $children, $color, $icon);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["paragraph"] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "children"  => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
            "color"     => $this->color->value,
        ];

        // Icons are only accepted on tab items, so the key is only sent when needed
        if ($this->icon !== null) {
            $array["paragraph"]["icon"] = $this->icon->toArray();
        } elseif ($this->iconRemoved) {
            $array["paragraph"]["icon"] = null;
        }

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
        return new self($this->metadata, $text, $this->children, $this->color, $this->icon, $this->iconRemoved);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->children, $this->color, $this->icon, $this->iconRemoved);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            $this->text,
            $children,
            $this->color,
            $this->icon,
            $this->iconRemoved,
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
            $this->icon,
            $this->iconRemoved,
        );
    }

    public function changeColor(Color $color): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $this->children,
            $color,
            $this->icon,
            $this->iconRemoved,
        );
    }

    /** @psalm-assert-if-true Icon $this->icon */
    public function hasIcon(): bool
    {
        return $this->icon !== null;
    }

    /**
     * Change the paragraph icon.
     *
     * Notion only accepts icons on paragraphs that are direct children of a
     * tab block (tab items). Icons on other paragraphs are rejected by the API.
     */
    public function changeIcon(Emoji|File|Icon $icon): self
    {
        if ($icon instanceof Emoji) {
            $icon = Icon::fromEmoji($icon);
        }

        if ($icon instanceof File) {
            $icon = Icon::fromFile($icon);
        }

        return new self(
            $this->metadata->update(),
            $this->text,
            $this->children,
            $this->color,
            $icon,
        );
    }

    public function removeIcon(): self
    {
        return new self(
            $this->metadata->update(),
            $this->text,
            $this->children,
            $this->color,
            null,
            $this->icon !== null || $this->iconRemoved,
        );
    }

    public function delete(): BlockInterface
    {
        return new self(
            $this->metadata->delete(),
            $this->text,
            $this->children,
            $this->color,
            $this->icon,
            $this->iconRemoved,
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
