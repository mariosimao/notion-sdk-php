<?php

namespace Notion\Blocks;

use Exception;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\RichText;

class Callout implements BlockInterface
{
    private const TYPE = Block::TYPE_CALLOUT;

    private Block $block;

    /** @var \Notion\Common\RichText[] */
    private array $text;

    private Emoji|File $icon;

    /** @var \Notion\Blocks\BlockInterface[] */
    private array $children;

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

    public static function fromArray(array $array): self
    {
        $block = Block::fromArray($array);

        $callout = $array[self::TYPE];

        $text = array_map(fn($t) => RichText::fromArray($t), $callout["text"]);

        $icon = match($callout["icon"]["type"]) {
            "emoji" => Emoji::fromArray($array["icon"]),
            "file"  => File::fromArray($array["icon"]),
            default => throw new Exception("Invalid icon type"),
        };

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

    public function children(): array
    {
        return $this->children;
    }

    public function withText(RichText ...$text): self
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

    public function withChildren(BlockInterface ...$children): self
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
