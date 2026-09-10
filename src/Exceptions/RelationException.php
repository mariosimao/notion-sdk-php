<?php

namespace Notion\Exceptions;

final class RelationException extends NotionException
{
    public static function emptySyncedPropertyName(): self
    {
        return new self("Bidirectional relations must provide 'synced property name'.");
    }

    public static function emptySyncedPropertyId(): self
    {
        return new self("Bidirectional relations must provide 'synced property ID'.");
    }
}
