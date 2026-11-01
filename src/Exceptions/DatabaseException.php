<?php

namespace Notion\Exceptions;

use Notion\Databases\DatabaseType;

final class DatabaseException extends NotionException
{
    public static function internalCover(): self
    {
        return new self("Internal cover image is not supported.");
    }

    public static function noTitleProperty(): self
    {
        return new self("A database must have a title property.");
    }

    public static function databaseTypeNotCreatable(DatabaseType $databaseType): self
    {
        return new self(
            "Database type \"{$databaseType->value}\" cannot be used to create a database. "
            . "Supported types are \"tasks\", \"projects\" and \"skills\".",
        );
    }

    public static function typedDatabaseWithInitialProperties(): self
    {
        return new self(
            "Typed databases use Notion's canonical schema and cannot be created with initial properties. "
            . "Update the data source after creating the database to add properties.",
        );
    }
}
