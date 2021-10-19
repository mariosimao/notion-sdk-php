<?php

namespace Notion\Blocks;

use Exception;

class BlockFactory
{
    /**
     * @param array{ type: string } $array
     */
    public static function fromArray(array $array): BlockInterface
    {
        $type = $array["type"];

        return match($type) {
            Block::TYPE_PARAGRAPH => Paragraph::fromArray($array),
            Block::TYPE_HEADING_1 => Heading1::fromArray($array),
            Block::TYPE_HEADING_2 => Heading2::fromArray($array),
            Block::TYPE_HEADING_3 => Heading3::fromArray($array),
            default => throw new Exception("Invalid block type '{$type}'"),
        };
    }
}
