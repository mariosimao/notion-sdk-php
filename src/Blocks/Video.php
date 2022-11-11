<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\File;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type VideoJson = array{ video: FileJson }
 *
 * @psalm-immutable
 */
class Video implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly File $file,
    ) {
        $metadata->checkType(BlockType::Video);
    }

    public static function fromFile(File $file): self
    {
        $block = BlockMetadata::create(BlockType::Video);

        return new self($block, $file);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var VideoJson $array */
        $file = File::fromArray($array["video"]);

        return new self($block, $file);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["video"] = $this->file->toArray();

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function changeFile(File $file): self
    {
        return new self($this->metadata, $file);
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
            $this->file,
        );
    }
}
