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
            Block::TYPE_PARAGRAPH          => Paragraph::fromArray($array),
            Block::TYPE_HEADING_1          => Heading1::fromArray($array),
            Block::TYPE_HEADING_2          => Heading2::fromArray($array),
            Block::TYPE_HEADING_3          => Heading3::fromArray($array),
            Block::TYPE_CALLOUT            => Callout::fromArray($array),
            Block::TYPE_QUOTE              => Quote::fromArray($array),
            Block::TYPE_BULLETED_LIST_ITEM => BulletedListItem::fromArray($array),
            Block::TYPE_NUMBERED_LIST_ITEM => NumberedListItem::fromArray($array),
            Block::TYPE_TO_DO              => ToDo::fromArray($array),
            Block::TYPE_TOGGLE             => Toggle::fromArray($array),
            Block::TYPE_CODE               => Code::fromArray($array),
            Block::TYPE_CHILD_PAGE         => ChildPage::fromArray($array),
            Block::TYPE_CHILD_DATABASE     => ChildDatabase::fromArray($array),
            Block::TYPE_EMBED              => Embed::fromArray($array),
            Block::TYPE_IMAGE              => Image::fromArray($array),
            Block::TYPE_VIDEO              => Video::fromArray($array),
            Block::TYPE_FILE               => FileBlock::fromArray($array),
            default => throw new Exception("Invalid block type '{$type}'"),
        };
    }
}
