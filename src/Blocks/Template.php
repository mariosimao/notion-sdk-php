<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type TemplateJson = array{
 *      template: array{
 *          rich_text: list<RichTextJson>,
 *          children?: list<array{ type: string, ... }>,
 *      },
 * }
 *
 * @psalm-immutable
 */
final readonly class Template implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param BlockInterface[] $children
     */
    private function __construct(
        private BlockMetadata $metadata,
        public array $text,
        public array $children,
    ) {
        $metadata->checkType(BlockType::Template);
    }

    public static function create(RichText ...$text): self
    {
        $block = BlockMetadata::create(BlockType::Template);

        return new self($block, $text, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var TemplateJson $array */
        $template = $array["template"];

        $text = array_map(fn($t) => RichText::fromArray($t), $template["rich_text"] ?? []);

        $children = array_map(fn($b) => BlockFactory::fromArray($b), $template["children"] ?? []);

        return new self($block, $text, $children);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["template"] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
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
        return new self($this->metadata, $text, $this->children);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->children);
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        $hasChildren = (count($children) > 0);

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            $this->text,
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
            $children,
        );
    }

    public function delete(): BlockInterface
    {
        return new self(
            $this->metadata->delete(),
            $this->text,
            $this->children,
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
