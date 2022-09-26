<?php

namespace Notion\Exceptions;

class IconException extends NotionException
{
    public static function bothNull(): self
    {
        return new self("Icon must be either emoji or file, not both null.");
    }

    public static function bothSet(): self
    {
        return new self("Icon must be either emoji or file, not both.");
    }
}
