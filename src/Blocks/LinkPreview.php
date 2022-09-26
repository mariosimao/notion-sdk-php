<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\NotionException;

/**
 * Link Preview block.
 *
 * This block cannot be created, only retrieved by the API.
 *
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type LinkPreviewJson = array{
 *      link_preview: array{ url: string },
 * }
 *
 * @psalm-immutable
 */
class LinkPreview implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly string $url
    ) {
        $metadata->checkType(BlockType::LinkPreview);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var LinkPreviewJson $array */
        $url = $array["link_preview"]["url"];

        return new self($block, $url);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["link_preview"] = [ "url" => $this->url ];

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            "link_preview" => [
                "url" => $this->url
            ],
            "archived" => $this->metadata()->archived,
        ];
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
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
            $this->url,
        );
    }
}
