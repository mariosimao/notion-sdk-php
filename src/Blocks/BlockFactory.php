<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;

class BlockFactory
{
    /**
     * @param array{ type: string } $array
     */
    public static function fromArray(array $array): BlockInterface
    {
        $type = $array["type"];

        return match ($type) {
            BlockType::Paragraph->value        => Paragraph::fromArray($array),
            BlockType::Heading1->value         => Heading1::fromArray($array),
            BlockType::Heading2->value         => Heading2::fromArray($array),
            BlockType::Heading3->value         => Heading3::fromArray($array),
            BlockType::Callout->value          => Callout::fromArray($array),
            BlockType::Quote->value            => Quote::fromArray($array),
            BlockType::BulletedListItem->value => BulletedListItem::fromArray($array),
            BlockType::NumberedListItem->value => NumberedListItem::fromArray($array),
            BlockType::ToDo->value             => ToDo::fromArray($array),
            BlockType::Toggle->value           => Toggle::fromArray($array),
            BlockType::Code->value             => Code::fromArray($array),
            BlockType::ChildPage->value        => ChildPage::fromArray($array),
            BlockType::ChildDatabase->value    => ChildDatabase::fromArray($array),
            BlockType::Embed->value            => Embed::fromArray($array),
            BlockType::Image->value            => Image::fromArray($array),
            BlockType::Video->value            => Video::fromArray($array),
            BlockType::File->value             => FileBlock::fromArray($array),
            BlockType::Pdf->value              => Pdf::fromArray($array),
            BlockType::Bookmark->value         => Bookmark::fromArray($array),
            BlockType::Equation->value         => EquationBlock::fromArray($array),
            BlockType::Divider->value          => Divider::fromArray($array),
            BlockType::TableOfContents->value  => TableOfContents::fromArray($array),
            BlockType::Breadcrumb->value       => Breadcrumb::fromArray($array),
            BlockType::Column->value           => Column::fromArray($array),
            BlockType::ColumnList->value       => ColumnList::fromArray($array),
            BlockType::LinkPreview->value      => LinkPreview::fromArray($array),
            default                            => throw BlockException::invalidType($type),
        };
    }
}
