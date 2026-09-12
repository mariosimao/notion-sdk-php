<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\LastEditedByPropertyItem;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class LastEditedByPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $user = User::create("user-id");
        $item = LastEditedByPropertyItem::create($user, "leb-id");

        $this->assertSame("leb-id", $item->metadata()->id);
        $this->assertSame(PropertyType::LastEditedBy, $item->metadata()->type);
        $this->assertSame($user, $item->user);
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "leb-id",
            "type" => "last_edited_by",
            "last_edited_by" => [
                "object" => "user",
                "id" => "23345d4f-cf71-4a70-89a5-226c95a6eaae",
                "name" => "Test User",
                "avatar_url" => null,
                "type" => "person",
                "person" => [
                    "email" => "avo@example.org",
                ],
            ],
        ];

        $item = LastEditedByPropertyItem::fromArray($array);

        $this->assertSame("leb-id", $item->metadata()->id);
        $this->assertSame("Test User", $item->user->name);
        $this->assertEquals([
            "object" => "property_item",
            "id" => "leb-id",
            "type" => "last_edited_by",
            "last_edited_by" => [
                "object" => "user",
                "id" => "23345d4f-cf71-4a70-89a5-226c95a6eaae",
                "name" => "Test User",
                "type" => "person",
                "person" => [
                    "email" => "avo@example.org",
                ],
            ],
        ], $item->toArray());
    }
}
