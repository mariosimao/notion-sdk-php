<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\File;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type FileBlockMetadataJson = array{ file: FileJson }
 *
 * @psalm-immutable
 */
class FileBlock implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        private readonly File $file,
    ) {
        $metadata->checkType(BlockType::File);
    }

    public static function fromFile(File $file): self
    {
        $metadata = BlockMetadata::create(BlockType::File);

        return new self($metadata, $file);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var FileBlockMetadataJson $array */
        $file = File::fromArray($array["file"]);

        return new self($metadata, $file);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["file"] = $this->file->toArray();

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function file(): File
    {
        return $this->file;
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
