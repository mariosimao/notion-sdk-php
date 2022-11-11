<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type TableOfContentsJson = array{
 *      table_of_contents: array<empty, empty>
 * }
 *
 * @psalm-immutable
 */
class TableOfContents implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
    ) {
        $metadata->checkType(BlockType::TableOfContents);
    }

    public static function create(): self
    {
        $block = BlockMetadata::create(BlockType::TableOfContents);

        return new self($block);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        return new self($block);
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

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
        );
    }
}
