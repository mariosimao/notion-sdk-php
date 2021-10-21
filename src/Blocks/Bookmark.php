<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type BookmarkJson = array{
 *      bookmark: array{
 *          url: string,
 *          caption: RichTextJson[],
 *      },
 * }
 */
class Bookmark implements BlockInterface
{
    private const TYPE = Block::TYPE_BOOKMARK;

    private Block $block;

    private string $url;

    /** @var \Notion\Common\RichText[] */
    private array $caption;

    /** @param \Notion\Common\RichText[] $caption */
    private function __construct(Block $block, string $url, array $caption) {
        if (!$block->isBookmark()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->url = $url;
        $this->caption = $caption;
    }

    public static function create(string $url): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, $url, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var BookmarkJson $array */
        $url = $array[self::TYPE]["url"];

        $caption = array_map(fn($t) => RichText::fromArray($t), $array[self::TYPE]["caption"]);

        return new self($block, $url, $caption);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "url" => $this->url,
            "caption" => array_map(fn(RichText $t) => $t->toArray(), $this->caption),
        ];

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

    /** @return \Notion\Common\RichText[] */
    public function caption(): array
    {
        return $this->caption;
    }

    public function withUrl(string $url): self
    {
        return new self($this->block, $url, $this->caption);
    }

    public function withCaption(RichText ...$caption): self
    {
        return new self($this->block, $this->url, $caption);
    }
}
