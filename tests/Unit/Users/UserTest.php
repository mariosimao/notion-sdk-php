<?php

namespace Notion\Test\Unit\Users;

use Notion\Users\User;
use Notion\Users\UserType;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_person_from_array(): void
    {
        $array = [
            "object"     => "user",
            "id"         => "b0688871-85db-4637-8fc9-043a240fcaec",
            "name"       => "Mario Simao",
            "avatar_url" => "http://example.com",
            "type"       => "person",
            "person"     => [ "email" => "mariosimao@email.com" ],
        ];

        $user = User::fromArray($array);

        $this->assertEquals($array, $user->toArray());
        $this->assertTrue($user->isPerson());
        $this->assertEquals("b0688871-85db-4637-8fc9-043a240fcaec", $user->id);
        $this->assertEquals("Mario Simao", $user->name);
        $this->assertEquals(UserType::Person, $user->type);
        $this->assertEquals("mariosimao@email.com", $user->person?->email);
    }

    public function test_bot_from_array(): void
    {
        $array = [
            "object"     => "user",
            "id"         => "b0688871-85db-4637-8fc9-043a240fcaec",
            "name"       => "Notion Bot",
            "type"       => "bot",
            "bot"        => [],
        ];

        $user = User::fromArray($array);

        $this->assertEquals($array, $user->toArray());
        $this->assertTrue($user->isBot());
        $this->assertNotNull($user->bot);
        $this->assertNull($user->avatarUrl);
    }

    public function test_invalid_type_from_array(): void
    {
        $array = [
            "object"     => "user",
            "id"         => "b0688871-85db-4637-8fc9-043a240fcaec",
            "name"       => "Invalid user",
            "type"       => "wrong-type",
        ];

        $this->expectException(\ValueError::class);
        /** @psalm-suppress InvalidArgument */
        User::fromArray($array);
    }
}
