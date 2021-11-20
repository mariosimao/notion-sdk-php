<?php

namespace Notion\Blocks;

use Notion\Common\RichText;

/**
 * @psalm-import-type BlockJson from Block
 *
 * @psalm-type EmbedJson = array{
 *      embed: array{ url: string },
 * }
 *
 * @psalm-immutable
 */
class Embed implements BlockInterface
{
    private const TYPE = Block::TYPE_EMBED;

    private Block $block;

    private string $url;

    private function __construct(Block $block, string $url)
    {
        if (!$block->isEmbed()) {
            throw new \Exception("Block must be of type " . self::TYPE);
        }

        $this->block = $block;
        $this->url = $url;
    }

    public static function create(string $url = ""): self
    {
        $block = Block::create(self::TYPE);

        return new self($block, $url);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var EmbedJson $array */
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

    public function withUrl(string $url): self
    {
        return new self($this->block, $url);
    }
}
