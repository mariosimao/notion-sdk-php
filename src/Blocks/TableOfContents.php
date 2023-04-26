<?php

namespace Notion\Blocks;

use Notion\Common\Color;
use Notion\Exceptions\BlockException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type TableOfContentsJson = array{
 *      table_of_contents: array{
 *          color?: string
 *      }
 * }
 *
 * @psalm-immutable
 */
class TableOfContents implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly Color $color,
    ) {
        $metadata->checkType(BlockType::TableOfContents);
    }

    public static function create(): self
    {
        $block = BlockMetadata::create(BlockType::TableOfContents);

        return new self($block, Color::Default);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var TableOfContentsJson $array */
        $toc = $array["table_of_contents"];

        $color = Color::tryFrom($toc["color"] ?? "") ?? Color::Default;

        return new self($block, $color);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["table_of_contents"] = new \stdClass();

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function addChild(BlockInterface $child): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function changeChildren(BlockInterface ...$children): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function changeColor(Color $color): self
    {
        return new self(
            $this->metadata->update(),
            $color,
        );
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->color,
        );
    }
}
