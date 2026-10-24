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
            "bot"        => [
                "object" => "bot",
                "workspace_limits" => [
                    "max_file_upload_size_in_bytes" => 104857600,
                ],
                "workspace_name" => "Mario's Workspace",
            ],
        ];

        $user = User::fromArray($array);

        $this->assertEquals($array, $user->toArray());
        $this->assertTrue($user->isBot());
        $this->assertNotNull($user->bot);
        $this->assertEquals("Mario's Workspace", $user->bot->workspaceName);
        $this->assertNull($user->avatarUrl);
    }

    public function test_user_owned_bot_has_null_workspace_name(): void
    {
        $array = [
            "object" => "user",
            "id"     => "b0688871-85db-4637-8fc9-043a240fcaec",
            "type"   => "bot",
            "bot"    => [
                "object" => "bot",
                "workspace_limits" => [
                    "max_file_upload_size_in_bytes" => 5242880,
                ],
                "workspace_name" => null,
            ],
        ];

        $user = User::fromArray($array);

        $this->assertNull($user->bot?->workspaceName);
        $this->assertEquals($array, $user->toArray());
    }

    public function test_bot_without_workspace_name_field(): void
    {
        $array = [
            "object" => "user",
            "id"     => "b0688871-85db-4637-8fc9-043a240fcaec",
            "type"   => "bot",
            "bot"    => [
                "object" => "bot",
                "workspace_limits" => [
                    "max_file_upload_size_in_bytes" => 5242880,
                ],
            ],
        ];

        $user = User::fromArray($array);

        $this->assertNull($user->bot?->workspaceName);
        $this->assertArrayHasKey("workspace_name", $user->toArray()["bot"] ?? []);
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
