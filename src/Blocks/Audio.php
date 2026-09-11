<?php

namespace Notion\Blocks;

use Notion\Common\File;
use Notion\Common\RichText;
use Notion\Exceptions\BlockException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type AudioJson = array{ audio: FileJson }
 *
 * @psalm-immutable
 */
final readonly class Audio implements BlockInterface
{
    private function __construct(
        private BlockMetadata $metadata,
        public File $file,
    ) {
        $metadata->checkType(BlockType::Audio);
    }

    public static function fromFile(File $file): self
    {
        $block = BlockMetadata::create(BlockType::Audio);

        return new self($block, $file);
    }

    public static function fromUrl(string $url): self
    {
        $file = File::createExternal($url);

        return self::fromFile($file);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var AudioJson $array */
        $file = File::fromArray($array["audio"]);

        return new self($block, $file);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["audio"] = $this->file->toArray();

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

    public function changeUrl(string $url): self
    {
        return new self($this->metadata, $this->file->changeUrl($url));
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

    public function delete(): BlockInterface
    {
        return new self(
            $this->metadata->delete(),
            $this->file,
        );
    }

    /**
     * @deprecated 1.17.0 Use `delete()` instead.
     * @codeCoverageIgnore
     */
    public function archive(): BlockInterface
    {
        return $this->delete();
    }
}
