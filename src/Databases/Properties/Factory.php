<?php

namespace Notion\Databases\Properties;

use Exception;

class Factory
{
    /**
     * @param array{ type: string } $array
     */
    public static function fromArray(array $array): PropertyInterface
    {
        $type = $array["type"];

        return match($type) {
            Property::TYPE_TITLE => Title::fromArray($array),
            Property::TYPE_RICH_TEXT => RichText::fromArray($array),
            Property::TYPE_NUMBER => Number::fromArray($array),
            Property::TYPE_SELECT => Select::fromArray($array),
            Property::TYPE_MULTI_SELECT => MultiSelect::fromArray($array),
            Property::TYPE_DATE => Date::fromArray($array),
            Property::TYPE_PEOPLE => People::fromArray($array),
            Property::TYPE_FILES => Files::fromArray($array),
            Property::TYPE_CHECKBOX => Checkbox::fromArray($array),
            default => throw new Exception("Invalid property type: '{$type}'"),
        };
    }
}
