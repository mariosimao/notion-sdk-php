<?php

namespace Notion\Blocks;

use Exception;

class BlockFactory
{
    public static function fromArray(array $array): BlockInterface
    {
        $type = $array["type"];

        return match($type) {
            Block::TYPE_PARAGRAPH => Paragraph::fromArray($array),
            default => throw new Exception("Invalid block type '{$type}'"),
        };
    }
}
