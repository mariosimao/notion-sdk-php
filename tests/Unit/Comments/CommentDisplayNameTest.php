<?php

namespace Notion\Test\Unit\Comments;

use Notion\Comments\CommentDisplayName;
use Notion\Comments\CommentDisplayNameType;
use PHPUnit\Framework\TestCase;

class CommentDisplayNameTest extends TestCase
{
    public function test_connection(): void
    {
        $name = CommentDisplayName::connection();

        $this->assertTrue($name->isConnection());
        $this->assertNull($name->resolvedName);
        $this->assertSame([ "type" => "connection" ], $name->toRequestArray());
    }

    public function test_user(): void
    {
        $name = CommentDisplayName::user();

        $this->assertTrue($name->isUser());
        $this->assertSame([ "type" => "user" ], $name->toRequestArray());
    }

    public function test_custom(): void
    {
        $name = CommentDisplayName::custom("Notion Bot");

        $this->assertTrue($name->isCustom());
        $this->assertSame("Notion Bot", $name->resolvedName);
        $this->assertSame(
            [ "type" => "custom", "custom" => [ "name" => "Notion Bot" ] ],
            $name->toRequestArray(),
        );
    }

    public function test_response_array_conversion(): void
    {
        $array = [ "type" => "user", "resolved_name" => "Avo Cado" ];
        $name = CommentDisplayName::fromArray($array);

        $this->assertSame(CommentDisplayNameType::User, $name->type);
        $this->assertSame("Avo Cado", $name->resolvedName);
        $this->assertSame($array, $name->toArray());
    }

    public function test_request_array_conversion(): void
    {
        $name = CommentDisplayName::fromArray([ "type" => "custom", "custom" => [ "name" => "Notion Bot" ] ]);

        $this->assertSame("Notion Bot", $name->resolvedName);
    }
}
