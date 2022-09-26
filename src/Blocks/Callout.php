<?php

namespace Notion\Blocks;

use Exception;
use Notion\Exceptions\BlockException;
use Notion\Common\Emoji;
use Notion\Common\File;
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
        public readonly Emoji|File $icon,
        public readonly array $children,
    ) {
        $metadata->checkType(BlockType::Callout);
    }

    public static function create(): self
    {
        $metadata = BlockMetadata::create(BlockType::Callout);
        $icon = Emoji::create("⭐");

        return new self($metadata, [], $icon, []);
    }

    public static function fromString(string $emoji, string $content): self
    {
        $metadata = BlockMetadata::create(BlockType::Callout);
        $text = [ RichText::createText($content) ];
        $icon = Emoji::create($emoji);

        return new self($metadata, $text, $icon, []);
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
            $icon = Emoji::fromArray($iconArray);
        } else {
            /** @psalm-var FileJson $iconArray */
            $icon = File::fromArray($iconArray);
        }

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $callout["children"] ?? []);

        return new self($metadata, $text, $icon, $children);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["callout"] = [
            "rich_text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "icon"     => $this->icon->toArray(),
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
        ];

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            "callout" => [
                "rich_text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
                "icon"     => $this->icon->toArray(),
            ],
            "archived" => $this->metadata()->archived,
        ];
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

    /**
     * @psalm-assert-if-true Emoji $this->icon
     * @psalm-assert-if-true Emoji $this->icon()
     */
    public function iconIsEmoji(): bool
    {
        return $this->icon::class === Emoji::class;
    }

    /**
     * @psalm-assert-if-true File $this->icon
     * @psalm-assert-if-true File $this->icon()
     */
    public function iconIsFile(): bool
    {
        return $this->icon::class === File::class;
    }

    public function changeText(RichText ...$text): self
    {
        return new self($this->metadata, $text, $this->icon, $this->children);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->icon, $this->children);
    }

    public function changeIcon(Emoji|File $icon): self
    {
        return new self($this->metadata, $this->text, $icon, $this->children);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            $this->text,
            $this->icon,
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
            $children,
        );
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->text,
            $this->icon,
            $this->children,
        );
    }
}
