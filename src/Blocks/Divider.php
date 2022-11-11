<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type DividerJson = array{
 *      divider: array<empty, empty>
 * }
 * @psalm-immutable
 */
class Divider implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
    ) {
        $metadata->checkType(BlockType::Divider);
    }

    public static function create(): self
    {
        $metadata = BlockMetadata::create(BlockType::Divider);

        return new self($metadata);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["divider"] = new \stdClass();

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
