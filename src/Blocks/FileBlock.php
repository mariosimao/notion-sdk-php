<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\File;
use Notion\NotionException;

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

    public static function create(File $file): self
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

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            "file" => $this->file->toArray(),
            "archived" => $this->metadata()->archived,
        ];
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
            $this->file,
        );
    }
}
