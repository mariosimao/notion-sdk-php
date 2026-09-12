<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\PeoplePropertyItem;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class PeoplePropertyItemTest extends TestCase
{
    public function test_create_single(): void
    {
        $user = User::create("user-123");
        $item = PeoplePropertyItem::create($user, "people-id");

        $this->assertSame("people-id", $item->metadata()->id);
        $this->assertSame(PropertyType::People, $item->metadata()->type);
        $this->assertSame($user, $item->user);
        $this->assertCount(1, $item->people);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array_single_object(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "people-id",
            "type" => "people",
            "people" => [
                "object" => "user",
                "id" => "285e5768-3fdc-4742-ab9e-125f9050f3b8",
                "name" => "Example Avo",
                "avatar_url" => null,
                "type" => "person",
                "person" => [
                    "email" => "avo@example.org",
                ],
            ],
        ];

        $item = PeoplePropertyItem::fromArray($array);

        $this->assertSame("people-id", $item->metadata()->id);
        $this->assertNotNull($item->user);
        $this->assertSame("Example Avo", $item->user->name);
        $this->assertEquals([
            "object" => "property_item",
            "id" => "people-id",
            "type" => "people",
            "people" => [
                "object" => "user",
                "id" => "285e5768-3fdc-4742-ab9e-125f9050f3b8",
                "name" => "Example Avo",
                "type" => "person",
                "person" => [
                    "email" => "avo@example.org",
                ],
            ],
        ], $item->toArray());
    }

    public function test_from_array_array_of_users(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "people-id",
            "type" => "people",
            "people" => [
                [
                    "object" => "user",
                    "id" => "285e5768-3fdc-4742-ab9e-125f9050f3b8",
                    "name" => "Example Avo",
                    "avatar_url" => null,
                    "type" => "person",
                    "person" => [
                        "email" => "avo@example.org",
                    ],
                ],
            ],
        ];

        $item = PeoplePropertyItem::fromArray($array);

        $this->assertCount(1, $item->people);
        $this->assertSame("Example Avo", $item->people[0]->name);
    }
}
