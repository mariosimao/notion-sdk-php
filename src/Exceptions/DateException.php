<?php

namespace Notion\Exceptions;

final class DateException extends NotionException
{
    public static function invalidTimeZone(string $timeZone): self
    {
        return new self("The time zone '{$timeZone}' is not a valid IANA time-zone name.");
    }

    public static function utcOffsetNotAllowed(): self
    {
        return new self("Dates cannot contain UTC offsets when a named time zone is provided.");
    }

    public static function timeInformationRequired(): self
    {
        return new self("Dates cannot be dates without time information when a named time zone is provided.");
    }
}
