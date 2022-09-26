<?php

namespace Notion\Pages\Properties;

use Exception;

class PropertyFactory
{
    /**
     * @param array{ type: string } $array
     */
    public static function fromArray(array $array): PropertyInterface
    {
        $type = $array["type"];

        return match ($type) {
            PropertyType::RichText->value       => RichTextProperty::fromArray($array),
            PropertyType::Number->value         => Number::fromArray($array),
            PropertyType::Select->value         => Select::fromArray($array),
            PropertyType::MultiSelect->value    => MultiSelect::fromArray($array),
            PropertyType::Date->value           => Date::fromArray($array),
            PropertyType::Files->value          => Files::fromArray($array),
            PropertyType::Formula->value        => Formula::fromArray($array),
            PropertyType::Relation->value       => Relation::fromArray($array),
            PropertyType::Title->value          => Title::fromArray($array),
            PropertyType::People->value         => People::fromArray($array),
            PropertyType::Checkbox->value       => Checkbox::fromArray($array),
            PropertyType::Url->value            => Url::fromArray($array),
            PropertyType::Email->value          => Email::fromArray($array),
            PropertyType::PhoneNumber->value    => PhoneNumber::fromArray($array),
            PropertyType::CreatedTime->value    => CreatedTime::fromArray($array),
            PropertyType::CreatedBy->value      => CreatedBy::fromArray($array),
            PropertyType::LastEditedTime->value => LastEditedTime::fromArray($array),
            PropertyType::LastEditedBy->value   => LastEditedBy::fromArray($array),
            default                             => throw new Exception("Invalid property type: '{$type}'"),
        };
    }
}
