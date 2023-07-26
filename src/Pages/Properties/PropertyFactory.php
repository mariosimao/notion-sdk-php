<?php

namespace Notion\Pages\Properties;

class PropertyFactory
{
    /**
     * @param array{ type: string, ... } $array
     */
    public static function fromArray(array $array): PropertyInterface
    {
        $type = $array["type"];

        return match ($type) {
            PropertyType::Checkbox->value       => Checkbox::fromArray($array),
            PropertyType::CreatedBy->value      => CreatedBy::fromArray($array),
            PropertyType::CreatedTime->value    => CreatedTime::fromArray($array),
            PropertyType::Date->value           => Date::fromArray($array),
            PropertyType::Email->value          => Email::fromArray($array),
            PropertyType::Files->value          => Files::fromArray($array),
            PropertyType::Formula->value        => Formula::fromArray($array),
            PropertyType::LastEditedBy->value   => LastEditedBy::fromArray($array),
            PropertyType::LastEditedTime->value => LastEditedTime::fromArray($array),
            PropertyType::MultiSelect->value    => MultiSelect::fromArray($array),
            PropertyType::Number->value         => Number::fromArray($array),
            PropertyType::People->value         => People::fromArray($array),
            PropertyType::PhoneNumber->value    => PhoneNumber::fromArray($array),
            PropertyType::Relation->value       => Relation::fromArray($array),
            PropertyType::RichText->value       => RichTextProperty::fromArray($array),
            PropertyType::Select->value         => Select::fromArray($array),
            PropertyType::Status->value         => Status::fromArray($array),
            PropertyType::Title->value          => Title::fromArray($array),
            PropertyType::UniqueId->value       => UniqueId::fromArray($array),
            PropertyType::Url->value            => Url::fromArray($array),
            default                             => Unknown::fromArray($array),
        };
    }
}
