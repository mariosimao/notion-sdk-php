<?php

namespace Notion\Exceptions;

final class ViewException extends NotionException
{
    public static function configurationTypeMismatch(string $viewType, string $configurationType): self
    {
        return new self(
            "Configuration of type \"{$configurationType}\" cannot be used on a view of type \"{$viewType}\"."
        );
    }

    public static function readOnlyConfiguration(): self
    {
        return new self("Dashboard configuration is read-only.");
    }

    public static function invalidStartDayOfWeek(): self
    {
        return new self("Start day of week must be 0 (Sunday) or 1 (Monday).");
    }

    public static function unsupportedGroupByOption(string $type): self
    {
        return new self("Group by of type \"{$type}\" does not support this option.");
    }

    public static function invalidPageSize(int $maxPageSize): self
    {
        return new self("Page size must be between 1 and {$maxPageSize}.");
    }
}
