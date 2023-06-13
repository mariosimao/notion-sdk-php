<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\File;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type ImageJson = array{ image: FileJson }
 *
 * @psalm-immutable
 */
class Image implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly File $file,
    ) {
        $metadata->checkType(BlockType::Image);
    }

    public static function fromFile(File $file): self
    {
        $block = BlockMetadata::create(BlockType::Image);

        return new self($block, $file);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var ImageJson $array */
        $file = File::fromArray($array["image"]);

        return new self($block, $file);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["image"] = $this->file->toArray();

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

    public function changeCaption(RichText ...$caption): self
    {
        return new self($this->metadata, $this->file->changeCaption(...$caption));
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
