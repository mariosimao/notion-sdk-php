<?php

namespace Notion\DataSources\Properties;

final readonly class PropertyFactory
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
            PropertyType::Rollup         => Rollup::fromArray($array),
            PropertyType::Select         => Select::fromArray($array),
            PropertyType::Status         => Status::fromArray($array),
            PropertyType::Title          => Title::fromArray($array),
            PropertyType::UniqueId       => UniqueId::fromArray($array),
            PropertyType::Url            => Url::fromArray($array),
            default                      => Unknown::fromArray($array),
        };
    }

    /**
     * Convert a property to the schema format accepted by create and update
     * requests, which may differ from the format returned by the API.
     *
     * @internal
     */
    public static function toRequestArray(PropertyInterface $property): array
    {
        if ($property instanceof Status) {
            return $property->toRequestArray();
        }

        return $property->toArray();
    }
}
