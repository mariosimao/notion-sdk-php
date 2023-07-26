<?php

namespace Notion\Databases\Properties;

class PropertyFactory
{
    /**
     * @param array{ type: string, ... } $array
     */
    public static function fromArray(array $array): PropertyInterface
    {
        $type = PropertyType::tryFrom($array["type"]);

        return match ($type) {
            PropertyType::Checkbox       => Checkbox::fromArray($array),
            PropertyType::CreatedBy      => CreatedBy::fromArray($array),
            PropertyType::CreatedTime    => CreatedTime::fromArray($array),
            PropertyType::Date           => Date::fromArray($array),
            PropertyType::Email          => Email::fromArray($array),
            PropertyType::Files          => Files::fromArray($array),
            PropertyType::Formula        => Formula::fromArray($array),
            PropertyType::LastEditedBy   => LastEditedBy::fromArray($array),
            PropertyType::LastEditedTime => LastEditedTime::fromArray($array),
            PropertyType::MultiSelect    => MultiSelect::fromArray($array),
            PropertyType::Number         => Number::fromArray($array),
            PropertyType::People         => People::fromArray($array),
            PropertyType::PhoneNumber    => PhoneNumber::fromArray($array),
            PropertyType::Relation       => Relation::fromArray($array),
            PropertyType::RichText       => RichTextProperty::fromArray($array),
            PropertyType::Select         => Select::fromArray($array),
            PropertyType::Status         => Status::fromArray($array),
            PropertyType::Title          => Title::fromArray($array),
            PropertyType::UniqueId       => UniqueId::fromArray($array),
            PropertyType::Url            => Url::fromArray($array),
            default                      => Unknown::fromArray($array),
        };
    }
}
