<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

final readonly class PropertyItemFactory
{
    /**
     * @param array<string, mixed> $array
     */
    public static function fromArray(array $array): PropertyItemInterface|PropertyItemList
    {
        $object = isset($array["object"]) && is_string($array["object"]) ? $array["object"] : null;

        if ($object === "list") {
            return PropertyItemList::fromArray($array);
        }

        $type = isset($array["type"]) && is_string($array["type"]) ? $array["type"] : "";

        return match ($type) {
            PropertyType::Title->value          => TitlePropertyItem::fromArray($array),
            PropertyType::RichText->value       => RichTextPropertyItem::fromArray($array),
            PropertyType::Number->value         => NumberPropertyItem::fromArray($array),
            PropertyType::Select->value         => SelectPropertyItem::fromArray($array),
            PropertyType::MultiSelect->value    => MultiSelectPropertyItem::fromArray($array),
            PropertyType::Date->value           => DatePropertyItem::fromArray($array),
            PropertyType::Formula->value        => FormulaPropertyItem::fromArray($array),
            PropertyType::Relation->value       => RelationPropertyItem::fromArray($array),
            PropertyType::Rollup->value         => RollupPropertyItem::fromArray($array),
            PropertyType::People->value         => PeoplePropertyItem::fromArray($array),
            PropertyType::Files->value          => FilesPropertyItem::fromArray($array),
            PropertyType::Checkbox->value       => CheckboxPropertyItem::fromArray($array),
            PropertyType::Url->value            => UrlPropertyItem::fromArray($array),
            PropertyType::Email->value          => EmailPropertyItem::fromArray($array),
            PropertyType::PhoneNumber->value    => PhoneNumberPropertyItem::fromArray($array),
            PropertyType::CreatedTime->value    => CreatedTimePropertyItem::fromArray($array),
            PropertyType::CreatedBy->value      => CreatedByPropertyItem::fromArray($array),
            PropertyType::LastEditedTime->value => LastEditedTimePropertyItem::fromArray($array),
            PropertyType::LastEditedBy->value   => LastEditedByPropertyItem::fromArray($array),
            PropertyType::Status->value         => StatusPropertyItem::fromArray($array),
            PropertyType::UniqueId->value       => UniqueIdPropertyItem::fromArray($array),
            default                             => UnknownPropertyItem::fromArray($array),
        };
    }
}
