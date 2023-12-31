<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\People;
use Notion\Pages\Properties\PropertyType;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class PeopleTest extends TestCase
{
    public function test_create(): void
    {
        $user1 = $this->user1();
        $user2 = $this->user2();

        $people = People::create($user1, $user2);

        $this->assertEquals([ $user1, $user2 ], $people->users);
        $this->assertTrue($people->metadata()->type === PropertyType::People);
    }

    public function test_replace_users(): void
    {
        $user1 = $this->user1();
        $user2 = $this->user2();

        $people = People::create($user1)->changePeople($user2);

        $this->assertEquals([ $user2 ], $people->users);
    }

    public function test_add_user(): void
    {
        $user1 = $this->user1();
        $user2 = $this->user2();

        $people = People::create($user1)->addPerson($user2);

        $this->assertEquals([ $user1, $user2 ], $people->users);
    }

    public function test_remove_user(): void
    {
        $user = $this->user1();

        $people = People::create($user);
        $people = $people->removePerson($user->id);

        $this->assertCount(0, $people->users);
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
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $people->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    private function user1(): User
    {
        return User::fromArray([
            "object"     => "user",
            "id" => "f98bfb6a-08b3-4e65-861b-6f68fb0c7a48",
            "name" => "Mario",
            "type" => "person",
            "person" => [ "email" => "mario@website.domain" ],
        ]);
    }

    private function user2(): User
    {
        return User::fromArray([
            "object"     => "user",
            "id" => "f98bfb6a-08b3-4e65-861b-6f68fb0c7a48",
            "name" => "Luigi",
            "type" => "person",
            "person" => [ "email" => "luigi@website.domain" ],
        ]);
    }
}
