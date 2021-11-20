<?php

namespace Notion\Blocks;

use Exception;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 * @psalm-import-type EmojiJson from \Notion\Common\Emoji
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type CalloutJson = array{
 *      callout: array{
 *          text: list<RichTextJson>,
 *          children: list<BlockJson>,
 *          icon: EmojiJson|FileJson,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Callout implements BlockInterface
{
    private const TYPE = Block::TYPE_CALLOUT;

    private Block $block;

    /** @var list<RichText> */
    private array $text;

    private Emoji|File $icon;

    /** @var list<\Notion\Blocks\BlockInterface> */
    private array $children;

    /**
     * @param list<RichText> $text
     * @param list<\Notion\Blocks\BlockInterface> $children
     */
    private function __construct(
        Block $block,
        array $text,
        Emoji|File $icon,
        array $children,
    ) {
        if (!$block->isCallout()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->text = $text;
        $this->icon = $icon;
        $this->children = $children;
    }

    public static function create(): self
    {
        $block = Block::create(self::TYPE);
        $icon = Emoji::create("⭐");

        return new self($block, [], $icon, []);
    }

    public static function fromString(string $emoji, string $content): self
    {
        $block = Block::create(self::TYPE);
        $text = [ RichText::createText($content) ];
        $icon = Emoji::create($emoji);

        return new self($block, $text, $icon, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var CalloutJson $array */
        $callout = $array[self::TYPE];

        $text = array_map(fn($t) => RichText::fromArray($t), $callout["text"]);

        $iconArray = $callout["icon"];
        if ($iconArray["type"] === "emoji") {
            /** @psalm-var EmojiJson $iconArray */
            $icon = Emoji::fromArray($iconArray);
        } else {
            /** @psalm-var FileJson $iconArray */
            $icon = File::fromArray($iconArray);
        }

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $callout["children"]);

        return new self($block, $text, $icon, $children);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "icon"     => $this->icon->toArray(),
            "children" => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
        ];

        return $array;
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

    public function icon(): Emoji|File
    {
        return $this->icon;
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

    /** @return list<BlockInterface> */
    public function children(): array
    {
        return $this->children;
    }

    /** @param list<RichText> $text */
    public function withText(array $text): self
    {
        return new self($this->block, $text, $this->icon, $this->children);
    }

    public function appendText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->block, $texts, $this->icon, $this->children);
    }

    public function withIcon(Emoji|File $icon): self
    {
        return new self($this->block, $this->text, $icon, $this->children);
    }

    /** @param list<BlockInterface> $children */
    public function withChildren(array $children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->block->withHasChildren($hasChildren),
            $this->text,
            $this->icon,
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
            $this->icon,
            $children,
        );
    }
}
