<?php

namespace Notion\Test\Unit\Authentication;

use Notion\Authentication\Owner;
use Notion\Authentication\OwnerType;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class OwnerTest extends TestCase
{
    public function test_user_owner(): void
    {
        $user = User::create("e2586e30-b3e6-42d7-a50e-e374526d56d7");
        $owner = Owner::user($user);

        $this->assertTrue($owner->isUser());
        $this->assertFalse($owner->isWorkspace());
        $this->assertSame(OwnerType::User, $owner->type);
        $this->assertSame($user, $owner->user);
    }

    public function test_workspace_owner(): void
    {
        $owner = Owner::workspace();

        $this->assertTrue($owner->isWorkspace());
        $this->assertFalse($owner->isUser());
        $this->assertSame(OwnerType::Workspace, $owner->type);
        $this->assertNull($owner->user);
    }

    public function test_from_array_user(): void
    {
        $array = [
            "type" => "user",
            "user" => [
                "object" => "user",
                "id" => "e2586e30-b3e6-42d7-a50e-e374526d56d7",
                "name" => "Jane Doe",
            ],
        ];

        $owner = Owner::fromArray($array);

        $this->assertTrue($owner->isUser());
        $this->assertSame("e2586e30-b3e6-42d7-a50e-e374526d56d7", $owner->user?->id);
        $this->assertSame("Jane Doe", $owner->user?->name);
    }

    public function test_from_array_workspace(): void
    {
        $array = [
            "type" => "workspace",
            "workspace" => true,
        ];

        $owner = Owner::fromArray($array);

        $this->assertTrue($owner->isWorkspace());
        $this->assertNull($owner->user);
    }
}
