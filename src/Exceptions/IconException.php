<?php

namespace Notion\Exceptions;

final class IconException extends NotionException
{
    public static function noneSet(): self
    {
        return new self("Icon must be an emoji, a custom emoji or a file.");
    }

    public static function multipleSet(): self
    {
        return new self("Icon must be only one of emoji, custom emoji or file.");
    }

    public static function unsupportedType(string $type): self
    {
        return new self("Icon type \"{$type}\" is not supported.");
    }
}
