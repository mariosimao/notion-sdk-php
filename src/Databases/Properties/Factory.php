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
            default => throw new Exception("Invalid property type: '{$type}'"),
        };
    }
}
