<?php

namespace Notion\Exceptions;

final class DateException extends NotionException
{
    public static function invalidTimeZone(string $timeZone): self
    {
        return new self(
            "'{$timeZone}' is not a named IANA time zone. UTC offsets and abbreviations are not supported.",
        );
    }
}
