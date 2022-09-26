<?php

namespace Notion\Exceptions;

class DatabaseException extends NotionException
{
    public static function internalCover(): self
    {
        return new self("Internal cover image is not supported.");
    }

    public static function noTitleProperty(): self
    {
        return new self("A database must have a title property.");
    }
}
