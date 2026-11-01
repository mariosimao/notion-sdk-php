<?php

namespace Notion\Test\Unit\Blocks\MeetingNotes\Query;

use Notion\Blocks\MeetingNotes\Query\Property;
use Notion\Blocks\MeetingNotes\Query\Sort;
use PHPUnit\Framework\TestCase;

class SortTest extends TestCase
{
    public function test_named_constructors(): void
    {
        $this->assertSame(Property::Title, Sort::title()->property);
        $this->assertSame(Property::Attendees, Sort::attendees()->property);
        $this->assertSame(Property::CreatedTime, Sort::createdTime()->property);
        $this->assertSame(Property::CreatedBy, Sort::createdBy()->property);
        $this->assertSame(Property::LastEditedTime, Sort::lastEditedTime()->property);
        $this->assertSame(Property::LastEditedBy, Sort::lastEditedBy()->property);
    }

    public function test_ascending_by_default(): void
    {
        $this->assertSame(
            [ "property" => "title", "direction" => "ascending" ],
            Sort::property(Property::Title)->toArray(),
        );
    }

    public function test_descending(): void
    {
        $this->assertSame(
            [ "property" => "created_time", "direction" => "descending" ],
            Sort::createdTime()->descending()->toArray(),
        );
    }

    public function test_ascending(): void
    {
        $this->assertSame(
            [ "property" => "created_time", "direction" => "ascending" ],
            Sort::createdTime()->descending()->ascending()->toArray(),
        );
    }
}
