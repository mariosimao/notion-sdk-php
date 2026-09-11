<?php

namespace Notion\Test\Unit\DataSources\Query;

use Notion\DataSources\Query\RelativeDate;
use PHPUnit\Framework\TestCase;

class RelativeDateTest extends TestCase
{
    public function test_values(): void
    {
        $this->assertSame("today", RelativeDate::Today->value);
        $this->assertSame("tomorrow", RelativeDate::Tomorrow->value);
        $this->assertSame("yesterday", RelativeDate::Yesterday->value);
        $this->assertSame("one_week_ago", RelativeDate::OneWeekAgo->value);
        $this->assertSame("one_week_from_now", RelativeDate::OneWeekFromNow->value);
        $this->assertSame("one_month_ago", RelativeDate::OneMonthAgo->value);
        $this->assertSame("one_month_from_now", RelativeDate::OneMonthFromNow->value);
    }
}
