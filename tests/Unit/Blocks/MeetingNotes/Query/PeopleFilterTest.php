<?php

namespace Notion\Test\Unit\Blocks\MeetingNotes\Query;

use InvalidArgumentException;
use Notion\Blocks\MeetingNotes\Query\Operator;
use Notion\Blocks\MeetingNotes\Query\PeopleFilter;
use Notion\Blocks\MeetingNotes\Query\Property;
use Notion\Blocks\MeetingNotes\Query\UserReference;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class PeopleFilterTest extends TestCase
{
    private const USER_1 = "3f2b25f3-0a52-4a90-8f0d-5a3f0e6c1d2e";
    private const USER_2 = "9b1b1c3e-0f4b-4d2b-8f2e-3f2b25f30a52";

    public function test_properties(): void
    {
        $this->assertSame(Property::Attendees, PeopleFilter::attendees()->property());
        $this->assertSame(Property::CreatedBy, PeopleFilter::createdBy()->property());
        $this->assertSame(Property::LastEditedBy, PeopleFilter::lastEditedBy()->property());
        $this->assertSame(Operator::IsNotEmpty, PeopleFilter::attendees()->operator());
    }

    public function test_attendees_contains_normalizes_users(): void
    {
        $filter = PeopleFilter::attendees()->contains(
            self::USER_1,
            "user://" . self::USER_1,
            User::create(self::USER_2),
            UserReference::me(),
        );

        $expected = [
            "property" => "attendees",
            "filter"   => [
                "operator" => "person_contains",
                "value"    => [
                    [ "type" => "exact", "value" => [ "table" => "notion_user", "id" => self::USER_1 ] ],
                    [ "type" => "exact", "value" => [ "table" => "notion_user", "id" => self::USER_2 ] ],
                    [ "type" => "relative", "value" => "me" ],
                ],
            ],
        ];

        $this->assertSame($expected, $filter->toArray());
        $this->assertCount(3, $filter->users());
    }

    public function test_does_not_contain(): void
    {
        $filter = PeopleFilter::createdBy()->doesNotContain(self::USER_1);

        $expected = [
            "property" => "created_by",
            "filter"   => [
                "operator" => "person_does_not_contain",
                "value"    => [
                    [ "type" => "exact", "value" => [ "table" => "notion_user", "id" => self::USER_1 ] ],
                ],
            ],
        ];

        $this->assertSame($expected, $filter->toArray());
    }

    public function test_contains_me(): void
    {
        $filter = PeopleFilter::lastEditedBy()->containsMe();

        $this->assertSame(
            [
                "property" => "last_edited_by",
                "filter"   => [
                    "operator" => "person_contains",
                    "value"    => [[ "type" => "relative", "value" => "me" ]],
                ],
            ],
            $filter->toArray(),
        );
    }

    public function test_empty_operators(): void
    {
        $this->assertSame(
            [ "property" => "attendees", "filter" => [ "operator" => "is_empty" ] ],
            PeopleFilter::attendees()->contains(self::USER_1)->isEmpty()->toArray(),
        );
        $this->assertSame(
            [ "property" => "attendees", "filter" => [ "operator" => "is_not_empty" ] ],
            PeopleFilter::attendees()->isNotEmpty()->toArray(),
        );
        $this->assertSame([], PeopleFilter::attendees()->isEmpty()->users());
    }

    public function test_requires_at_least_one_user(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PeopleFilter::attendees()->contains();
    }

    public function test_too_many_users(): void
    {
        $users = [];
        for ($i = 0; $i <= PeopleFilter::MAX_USERS; $i++) {
            $users[] = "user-{$i}";
        }

        $this->expectException(InvalidArgumentException::class);
        PeopleFilter::attendees()->contains(...$users);
    }
}
