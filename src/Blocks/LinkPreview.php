<?php

namespace Notion\Blocks;

use Notion\NotionException;

/**
 * Link Preview block.
 *
 * This block cannot be created, only retrieved by the API.
 *
 * @psalm-import-type BlockJson from Block
 *
 * @psalm-type LinkPreviewJson = array{
 *      link_preview: array{ url: non-empty-string },
 * }
 *
 * @psalm-immutable
 */
class LinkPreview implements BlockInterface
{
    private const TYPE = Block::TYPE_LINK_PREVIEW;

    private Block $block;

    /** @var non-empty-string */
    private string $url;

    /** @param non-empty-string $url */
    private function __construct(Block $block, string $url)
    {
        if (!$block->isLinkPreview()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->url = $url;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var LinkPreviewJson $array */
        $url = $array[self::TYPE]["url"];

        return new self($block, $url);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [ "url" => $this->url ];

        return $array;
    }

    public function block(): Block
    {
        return $this->block;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function changeChildren(array $children): self
    {
        throw new NotionException(
            "This block does not support children.",
            "no_children_support",
        );
    }
}
