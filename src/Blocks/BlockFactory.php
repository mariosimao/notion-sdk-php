<?php

namespace Notion\Blocks;

class BlockFactory
{
    /**
     * @param array{ type: string, ... } $array
     */
    public static function fromArray(array $array): BlockInterface
    {
        $type = $array["type"];

        return match ($type) {
            BlockType::Bookmark->value         => Bookmark::fromArray($array),
            BlockType::Breadcrumb->value       => Breadcrumb::fromArray($array),
            BlockType::BulletedListItem->value => BulletedListItem::fromArray($array),
            BlockType::Callout->value          => Callout::fromArray($array),
            BlockType::ChildDatabase->value    => ChildDatabase::fromArray($array),
            BlockType::ChildPage->value        => ChildPage::fromArray($array),
            BlockType::Code->value             => Code::fromArray($array),
            BlockType::Column->value           => Column::fromArray($array),
            BlockType::ColumnList->value       => ColumnList::fromArray($array),
            BlockType::Divider->value          => Divider::fromArray($array),
            BlockType::Embed->value            => Embed::fromArray($array),
            BlockType::Equation->value         => EquationBlock::fromArray($array),
            BlockType::File->value             => FileBlock::fromArray($array),
            BlockType::Heading1->value         => Heading1::fromArray($array),
            BlockType::Heading2->value         => Heading2::fromArray($array),
            BlockType::Heading3->value         => Heading3::fromArray($array),
            BlockType::Image->value            => Image::fromArray($array),
            BlockType::LinkPreview->value      => LinkPreview::fromArray($array),
            BlockType::NumberedListItem->value => NumberedListItem::fromArray($array),
            BlockType::Paragraph->value        => Paragraph::fromArray($array),
            BlockType::Pdf->value              => Pdf::fromArray($array),
            BlockType::Quote->value            => Quote::fromArray($array),
            BlockType::TableOfContents->value  => TableOfContents::fromArray($array),
            BlockType::ToDo->value             => ToDo::fromArray($array),
            BlockType::Toggle->value           => Toggle::fromArray($array),
            BlockType::Video->value            => Video::fromArray($array),
            default                            => Unknown::fromArray($array),
        };
    }
}
