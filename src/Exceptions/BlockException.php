<?php

namespace Notion\Exceptions;

use Notion\Blocks\BlockType;
use Notion\Exceptions\NotionException;

class BlockException extends NotionException
{
    public static function wrongType(BlockType $expectedType): self
    {
        return new self("Block must be of type '{$expectedType->value}'");
    }

    public static function noChindrenSupport(): self
    {
        return new self("This block does not support children.");
    }
}
