<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type ToDoJson = array{
 *      to_do: array{
 *          checked: bool,
 *          text: list<RichTextJson>,
 *          children: list<BlockJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class ToDo implements BlockInterface
{
    private const TYPE = Block::TYPE_TO_DO;

    private Block $block;

    /** @var list<RichText> */
    private array $text;

    private bool $checked;

    /** @var list<\Notion\Blocks\BlockInterface> */
    private array $children;

    /**
     * @param list<RichText> $text
     * @param list<\Notion\Blocks\BlockInterface> $children
     */
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

    public static function fromString(string $content): self
    {
        $block = Block::create(self::TYPE);
        $text = [ RichText::createText($content) ];

        return new self($block, $text, false, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var ToDoJson $array */
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

    /** @return list<BlockInterface> */
    public function children(): array
    {
        return $this->children;
    }

    /** @param list<RichText> $text */
    public function withText(array $text): self
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

    /** @param list<BlockInterface> $children */
    public function withChildren(array $children): self
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
