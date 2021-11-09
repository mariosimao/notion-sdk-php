<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\Factory;
use Notion\Pages\Properties\People;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class PeopleTest extends TestCase
{
    public function test_create(): void
    {
        $user1 = $this->user1();
        $user2 = $this->user2();

        $people = People::create([ $user1, $user2 ]);

        $this->assertEquals([ $user1, $user2 ], $people->users());
        $this->assertTrue($people->property()->isPeople());
    }

    public function test_replace_users(): void
    {
        $user1 = $this->user1();
        $user2 = $this->user2();

        $people = People::create([ $user1 ])->withPeople([ $user2 ]);

        $this->assertEquals([ $user2 ], $people->users());
    }

    public function test_add_user(): void
    {
        $user1 = $this->user1();
        $user2 = $this->user2();

        $people = People::create([ $user1 ])->addPerson($user2);

        $this->assertEquals([ $user1, $user2 ], $people->users());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "people",
            "people" => [
                $this->user1()->toArray(),
                $this->user2()->toArray(),
            ],
        ];

        $people = People::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $people->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    private function user1(): User
    {
        return User::fromArray([
            "id" => "f98bfb6a-08b3-4e65-861b-6f68fb0c7a48",
            "name" => "Mario",
            "avatar_url" => null,
            "type" => "person",
            "person" => [ "email" => "mario@website.domain" ],
        ]);
    }

    private function user2(): User
    {
        return User::fromArray([
            "id" => "f98bfb6a-08b3-4e65-861b-6f68fb0c7a48",
            "name" => "Luigi",
            "avatar_url" => null,
            "type" => "person",
            "person" => [ "email" => "luigi@website.domain" ],
        ]);
    }
}
