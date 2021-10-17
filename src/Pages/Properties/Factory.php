<?php

namespace Notion\Pages\Properties;

use Exception;

class Factory
{
    public static function fromArray(array $array): Title
    {
        $type = $array["type"];

        return match($type) {
            "title" => Title::fromArray($array),
            default => throw new Exception("Invalid property type: '{$type}'"),
        };
    }
}
