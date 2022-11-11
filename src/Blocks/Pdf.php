<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\File;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type PdfJson = array{ pdf: FileJson }
 *
 * @psalm-immutable
 */
class Pdf implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly File $file
    ) {
        $metadata->checkType(BlockType::Pdf);
    }

    public static function fromFile(File $file): self
    {
        $block = BlockMetadata::create(BlockType::Pdf);

        return new self($block, $file);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var PdfJson $array */
        $file = File::fromArray($array["pdf"]);

        return new self($block, $file);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["pdf"] = $this->file->toArray();

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
