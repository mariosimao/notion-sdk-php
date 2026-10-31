<?php

namespace Notion\Exceptions;

final class IconException extends NotionException
{
    public static function bothNull(): self
    {
        return new self("Icon must be an emoji, a file or a native icon, none was given.");
    }

    public static function bothSet(): self
    {
        return new self("Icon must be only one of emoji, file or native icon, not multiple.");
    }

    public static function unsupportedType(string $type): self
    {
        return new self("Icon type \"{$type}\" is not supported.");
    }
}
