<?php

namespace Notion\Blocks;

use Notion\Blocks\Exceptions\BlockException;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type Heading1Json = array{
 *      heading_1: array{
 *          rich_text: list<RichTextJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Heading1 implements BlockInterface
{
    /**
     * @param RichText[] $text
     */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $text
    ) {
        $metadata->checkType(BlockType::Heading1);
    }

    public static function create(RichText ...$text): self
    {
        $block = BlockMetadata::create(BlockType::Heading1);

        return new self($block, $text);
    }

    public static function fromString(string $content): self
    {
        $block = BlockMetadata::create(BlockType::Heading1);
        $text = [ RichText::createText($content) ];

        return new self($block, $text);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var Heading1Json $array */
        $heading = $array["heading_1"];

        $text = array_map(fn($t) => RichText::fromArray($t), $heading["rich_text"]);

        return new self($block, $text);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["heading_1"] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
        ];

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            "heading_1" => [
                "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
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

    /** @param RichText[] $text */
    public function changeText(array $text): self
    {
        return new self($this->metadata, $text);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts);
    }

    public function addChild(BlockInterface $child): self
    {
        throw BlockException::noChindrenSupport();
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        throw BlockException::noChindrenSupport();
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->text,
        );
    }
}
