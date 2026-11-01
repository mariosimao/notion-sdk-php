<?php

namespace Notion\Test\Unit\Blocks\MeetingNotes\Query;

use InvalidArgumentException;
use Notion\Blocks\MeetingNotes\Query\UserReference;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class UserReferenceTest extends TestCase
{
    private const USER_ID = "3f2b25f3-0a52-4a90-8f0d-5a3f0e6c1d2e";

    public function test_id(): void
    {
        $user = UserReference::id(self::USER_ID);

        $this->assertSame(self::USER_ID, $user->userId);
        $this->assertFalse($user->isMe());
        $this->assertSame(
            [ "type" => "exact", "value" => [ "table" => "notion_user", "id" => self::USER_ID ] ],
            $user->toArray(),
        );
    }

    public function test_user_uri_is_normalized(): void
    {
        $user = UserReference::id(" user://" . self::USER_ID . " ");

        $this->assertSame(self::USER_ID, $user->userId);
    }

    public function test_from_user(): void
    {
        $user = UserReference::user(User::create(self::USER_ID));

        $this->assertSame(self::USER_ID, $user->userId);
    }

    public function test_me(): void
    {
        $user = UserReference::me();

        $this->assertTrue($user->isMe());
        $this->assertNull($user->userId);
        $this->assertSame([ "type" => "relative", "value" => "me" ], $user->toArray());
    }

    public function test_normalize(): void
    {
        $me = UserReference::me();

        $this->assertSame($me, UserReference::normalize($me));
        $this->assertSame(self::USER_ID, UserReference::normalize(User::create(self::USER_ID))->userId);
        $this->assertSame(self::USER_ID, UserReference::normalize("user://" . self::USER_ID)->userId);
    }

    public function test_empty_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        UserReference::id("user://");
    }
}
