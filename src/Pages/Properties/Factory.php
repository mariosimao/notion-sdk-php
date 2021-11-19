<?php

namespace Notion\Pages\Properties;

use Exception;

class Factory
{
    /**
     * @param array{ type: string } $array
     */
    public static function fromArray(array $array): PropertyInterface
    {
        $type = $array["type"];

        return match ($type) {
            Property::TYPE_RICH_TEXT => RichTextProperty::fromArray($array),
            Property::TYPE_NUMBER    => Number::fromArray($array),
            Property::TYPE_SELECT    => Select::fromArray($array),
            Property::TYPE_MULTI_SELECT => MultiSelect::fromArray($array),
            Property::TYPE_DATE => Date::fromArray($array),
            Property::TYPE_FORMULA => Formula::fromArray($array),
            Property::TYPE_RELATION => Relation::fromArray($array),
            Property::TYPE_TITLE     => Title::fromArray($array),
            Property::TYPE_PEOPLE => People::fromArray($array),
            Property::TYPE_CHECKBOX => Checkbox::fromArray($array),
            Property::TYPE_URL => Url::fromArray($array),
            Property::TYPE_EMAIL => Email::fromArray($array),
            Property::TYPE_PHONE_NUMBER => PhoneNumber::fromArray($array),
            Property::TYPE_CREATED_TIME => CreatedTime::fromArray($array),
            Property::TYPE_CREATED_BY => CreatedBy::fromArray($array),
            Property::TYPE_LAST_EDITED_TIME => LastEditedTime::fromArray($array),
            Property::TYPE_LAST_EDITED_BY => LastEditedBy::fromArray($array),
            default => throw new Exception("Invalid property type: '{$type}'"),
        };
    }
}
