<?php

namespace Notion\Test\Unit\Exceptions;

use Notion\Exceptions\DateException;
use Notion\Exceptions\NotionException;
use PHPUnit\Framework\TestCase;

class DateExceptionTest extends TestCase
{
    public function test_invalid_time_zone(): void
    {
        $e = DateException::invalidTimeZone("Invalid/Zone");

        $this->assertInstanceOf(NotionException::class, $e);
        $this->assertSame("The time zone 'Invalid/Zone' is not a valid IANA time-zone name.", $e->getMessage());
    }

    public function test_utc_offset_not_allowed(): void
    {
        $e = DateException::utcOffsetNotAllowed();

        $this->assertInstanceOf(NotionException::class, $e);
        $this->assertSame("Dates cannot contain UTC offsets when a named time zone is provided.", $e->getMessage());
    }

    public function test_time_information_required(): void
    {
        $e = DateException::timeInformationRequired();

        $this->assertInstanceOf(NotionException::class, $e);
        $expected = "Dates cannot be dates without time information when a named time zone is provided.";
        $this->assertSame($expected, $e->getMessage());
    }
}
