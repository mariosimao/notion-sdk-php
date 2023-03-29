<?php

namespace Notion\Test\Integration;

use Notion\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

class UsersTest extends TestCase
{
    public function test_find_current_user(): void
    {
        $client = Helper::client();

        $user = $client->users()->me();
        $sameUser = $client->users()->find($user->id);

        $this->assertTrue($user->isBot());
        $this->assertEquals($user, $sameUser);
    }

    public function test_find_all_users(): void
    {
        $client = Helper::client();

        $users = $client->users()->findAll();

        $this->assertTrue(count($users) > 1);
    }

    public function test_find_inexistent_user(): void
    {
        $client = Helper::client();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage(
            "Could not find user with ID: 7c3bd31e-63fa-4c60-956d-2264ceb2c522."
        );
        $client->users()->find("7c3bd31e-63fa-4c60-956d-2264ceb2c522");
    }
}
