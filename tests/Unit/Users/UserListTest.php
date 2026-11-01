<?php

namespace Notion\Test\Unit\Users;

use Notion\Users\User;
use Notion\Users\UserList;
use PHPUnit\Framework\TestCase;

class UserListTest extends TestCase
{
    public function test_from_array(): void
    {
        $user = [
            "object"     => "user",
            "id"         => "b0688871-85db-4637-8fc9-043a240fcaec",
            "name"       => "Mario Simao",
            "avatar_url" => "http://example.com",
            "type"       => "person",
            "person"     => [ "email" => "mariosimao@email.com" ],
        ];

        $list = UserList::fromArray([
            "object"      => "list",
            "results"     => [ $user ],
            "next_cursor" => "fe2cc560-036c-44cd-90e8-294d5a74cebc",
            "has_more"    => true,
            "type"        => "user",
            "user"        => new \stdClass(),
        ]);

        $this->assertCount(1, $list->results);
        $this->assertEquals(User::fromArray($user), $list->results[0]);
        $this->assertSame("fe2cc560-036c-44cd-90e8-294d5a74cebc", $list->nextCursor);
        $this->assertTrue($list->hasMore);
    }

    public function test_from_array_last_page(): void
    {
        $list = UserList::fromArray([
            "results"     => [],
            "next_cursor" => null,
            "has_more"    => false,
        ]);

        $this->assertSame([], $list->results);
        $this->assertNull($list->nextCursor);
        $this->assertFalse($list->hasMore);
    }

    public function test_create(): void
    {
        $list = UserList::create([], "cursor", true);
        $this->assertSame("cursor", $list->nextCursor);
        $this->assertTrue($list->hasMore);

        $empty = UserList::create([]);
        $this->assertSame([], $empty->results);
        $this->assertNull($empty->nextCursor);
        $this->assertFalse($empty->hasMore);
    }
}
