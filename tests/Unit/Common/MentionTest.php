<?php

namespace Notion\Test\Unit\Common;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\Mention;
use Notion\Common\MentionType;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class MentionTest extends TestCase
{
    public function test_mention_page(): void
    {
        $mention = Mention::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");

        $this->assertTrue($mention->isPage());
        $this->assertEquals(MentionType::Page, $mention->type);
        $this->assertEquals("1ce62b6f-b7f3-4201-afd0-08acb02e61c6", $mention->pageId);
    }

    public function test_mention_database(): void
    {
        $mention = Mention::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");

        $this->assertTrue($mention->isDatabase());
        $this->assertEquals("1ce62b6f-b7f3-4201-afd0-08acb02e61c6", $mention->databaseId);
    }

    public function test_mention_user(): void
    {
        $user = User::fromArray([
            "object"     => "user",
            "id"         => "b0688871-85db-4637-8fc9-043a240fcaec",
            "name"       => "Mario Simao",
            "avatar_url" => "http://example.com",
            "type"       => "person",
            "person"     => [ "email" => "mariosimao@email.com" ],
        ]);

        $mention = Mention::user($user);

        $this->assertTrue($mention->isUser());
        $this->assertEquals($user, $mention->user);
    }

    public function test_mention_date(): void
    {
        $date = Date::create(new DateTimeImmutable("2021-01-01"));
        $mention = Mention::date($date);

        $this->assertTrue($mention->isDate());
        $this->assertEquals($date, $mention->date);
    }

    public function test_page_array_conversion(): void
    {
        $array = [
            "type" => "page",
            "page" => [ "id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6" ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_database_array_conversion(): void
    {
        $array = [
            "type" => "database",
            "database" => [ "id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6" ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_user_array_conversion(): void
    {
        $array = [
            "type" => "user",
            "user" => [
                "object"     => "user",
                "id"         => "b0688871-85db-4637-8fc9-043a240fcaec",
                "name"       => "Mario Simao",
                "avatar_url" => "http://example.com",
                "type"       => "person",
                "person"     => [ "email" => "mariosimao@email.com" ],
            ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_date_array_conversion(): void
    {
        $array = [
            "type" => "date",
            "date" => [ "start" => "2021-01-01T00:00:00.000000Z", "end" => null ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }
}
