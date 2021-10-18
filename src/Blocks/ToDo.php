<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

class ToDo implements BlockInterface
{
    private const TYPE = Block::TYPE_TO_DO;

    private Block $block;

    /** @var \Notion\Common\RichText[] */
    private array $text;

    private bool $checked;

    /** @var \Notion\Blocks\BlockInterface[] */
    private array $children;

    private function __construct(
        Block $block,
        array $text,
        bool $checked,
        array $children,
    ) {
        if (!$block->isToDo()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->text = $text;
        $this->checked = $checked;
        $this->children = $children;
    }

    public static function create(): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, [], false, []);
    }

    public static function fromString($content): self
    {
        $block = Block::create(self::TYPE);
        $text = [ RichText::createText($content) ];

        return new self($block, $text, false, []);
    }

    public static function fromArray(array $array): self
    {
        $block = Block::fromArray($array);

        $todo = $array[self::TYPE];

        $text = array_map(fn($t) => RichText::fromArray($t), $todo["text"]);

        $checked = $todo["checked"];

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $todo["children"]);

        return new self($block, $text, $checked, $children);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "checked"  => $this->checked,
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

    public function checked(): bool
    {
        return $this->checked;
    }

    public function children(): array
    {
        return $this->children;
    }

    public function withText(RichText ...$text): self
    {
        return new self($this->block, $text, $this->checked, $this->children);
    }

    public function appendText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->block, $texts, $this->checked, $this->children);
    }

    public function check(): self
    {
        return new self($this->block, $this->text, true, $this->children);
    }

    public function uncheck(): self
    {
        return new self($this->block, $this->text, false, $this->children);
    }

    public function withChildren(BlockInterface ...$children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->block->withHasChildren($hasChildren),
            $this->text,
            $this->checked,
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
            $this->checked,
            $children,
        );
    }
}
