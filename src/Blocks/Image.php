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
        public readonly array $caption
    ) {
        $metadata->checkType(BlockType::Image);
    }

    public static function fromFile(File $file, RichText ...$caption): self
    {
        $block = BlockMetadata::create(BlockType::Image);

        return new self($block, $file, $caption);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var ImageJson $array */
        $file = File::fromArray($array["image"]);

        /** @psalm-var RichTextJson $array */
        $caption = array_map(fn($t) => RichText::fromArray($t), $array["image"]["caption"]);

        return new self($block, $file, $caption);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["image"] = $this->file->toArray();

        $array["image"]["caption"] = array_map(fn(RichText $t) => $t->toArray(), $this->caption);

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function changeFile(File $file): self
    {
        return new self($this->metadata, $file, $this->caption);
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
            $this->caption,
        );
    }
}
