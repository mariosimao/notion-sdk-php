<?php

namespace Notion\Test\Unit\Common;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\Mention;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class MentionTest extends TestCase
{
    public function test_mention_page(): void
    {
        $mention = Mention::createPage("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");

        $this->assertTrue($mention->isPage());
        $this->assertEquals("page", $mention->type());
        $this->assertEquals("1ce62b6f-b7f3-4201-afd0-08acb02e61c6", $mention->pageId());
    }

    public function test_mention_database(): void
    {
        $mention = Mention::createDatabase("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");

        $this->assertTrue($mention->isDatabase());
        $this->assertEquals("database", $mention->type());
        $this->assertEquals("1ce62b6f-b7f3-4201-afd0-08acb02e61c6", $mention->databaseId());
    }

    public function test_mention_user(): void
    {
        $user = User::fromArray([
            "id"         => "b0688871-85db-4637-8fc9-043a240fcaec",
            "name"       => "Mario Simao",
            "avatar_url" => "http://example.com",
            "type"       => "person",
            "person"     => [ "email" => "mariosimao@email.com" ],
        ]);

        $mention = Mention::createUser($user);

        $this->assertTrue($mention->isUser());
        $this->assertEquals("user", $mention->type());
        $this->assertEquals($user, $mention->user());
    }

    public function test_mention_date(): void
    {
        $date = Date::create(new DateTimeImmutable("2021-01-01"));
        $mention = Mention::createDate($date);

        $this->assertTrue($mention->isDate());
        $this->assertEquals("date", $mention->type());
        $this->assertEquals($date, $mention->date());
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
            "date" => [ "start" => "2021-01-01", "end" => null ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_wrong_type_array_conversion(): void
    {
        $array = [ "type" => "wrong-type" ];

        $this->expectException(\Exception::class);
        /** @psalm-suppress InvalidArgument */
        Mention::fromArray($array);
    }
}
