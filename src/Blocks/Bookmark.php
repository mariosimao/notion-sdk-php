<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\RichText;

/**
 * Bookmark block
 *
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type BookmarkJson = array{
 *      bookmark: array{
 *          url: string,
 *          caption: RichTextJson[],
 *      },
 * }
 *
 * @psalm-immutable
 */
class Bookmark implements BlockInterface
{
    /** @param RichText[] $caption */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly string $url,
        public readonly array $caption
    ) {
        $metadata->checkType(BlockType::Bookmark);
    }

    /**
     * Create a bookmark from a URL
     */
    public static function fromUrl(string $url): self
    {
        $metadata = BlockMetadata::create(BlockType::Bookmark);

        return new self($metadata, $url, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var BookmarkJson $array */
        $url = $array["bookmark"]["url"];

        $caption = array_map(fn($t) => RichText::fromArray($t), $array["bookmark"]["caption"]);

        return new self($metadata, $url, $caption);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["bookmark"] = [
            "url" => $this->url,
            "caption" => array_map(fn(RichText $t) => $t->toArray(), $this->caption),
        ];

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    /** Change bookmark URL */
    public function changeUrl(string $url): self
    {
        return new self($this->metadata, $url, $this->caption);
    }

    /** Change bookmark caption */
    public function changeCaption(RichText ...$caption): self
    {
        return new self($this->metadata, $this->url, $caption);
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
            $this->url,
            $this->caption,
        );
    }
}
