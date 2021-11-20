<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * Bookmark block
 *
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type BookmarkJson = array{
 *      bookmark: array{
 *          url: string,
 *          caption: list<RichTextJson>,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Bookmark implements BlockInterface
{
    private const TYPE = Block::TYPE_BOOKMARK;

    private Block $block;

    private string $url;

    /** @var list<RichText> */
    private array $caption;

    /** @param list<RichText> $caption */
    private function __construct(Block $block, string $url, array $caption)
    {
        if (!$block->isBookmark()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->url = $url;
        $this->caption = $caption;
    }

    /**
     * Create a bookmark from a URL
     */
    public static function create(string $url): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, $url, []);
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var BookmarkJson $array */
        $url = $array[self::TYPE]["url"];

        $caption = array_map(fn($t) => RichText::fromArray($t), $array[self::TYPE]["caption"]);

        return new self($block, $url, $caption);
    }

    /** @internal */
    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = [
            "url" => $this->url,
            "caption" => array_map(fn(RichText $t) => $t->toArray(), $this->caption),
        ];

        return $array;
    }

    /** Get block common object */
    public function block(): Block
    {
        return $this->block;
    }

    /** Get bookmark URL */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * Get bookmark caption
     *
     * @return list<RichText>
     */
    public function caption(): array
    {
        return $this->caption;
    }

    /**
     * Change bookmark URL
     */
    public function withUrl(string $url): self
    {
        return new self($this->block, $url, $this->caption);
    }

    /**
     * Change bookmark caption
     *
     * @param list<RichText> $caption
     */
    public function withCaption(array $caption): self
    {
        return new self($this->block, $this->url, $caption);
    }
}
