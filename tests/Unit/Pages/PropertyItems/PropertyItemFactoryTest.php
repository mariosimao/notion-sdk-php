<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\PropertyItems\CheckboxPropertyItem;
use Notion\Pages\PropertyItems\CreatedByPropertyItem;
use Notion\Pages\PropertyItems\CreatedTimePropertyItem;
use Notion\Pages\PropertyItems\DatePropertyItem;
use Notion\Pages\PropertyItems\EmailPropertyItem;
use Notion\Pages\PropertyItems\FilesPropertyItem;
use Notion\Pages\PropertyItems\FormulaPropertyItem;
use Notion\Pages\PropertyItems\LastEditedByPropertyItem;
use Notion\Pages\PropertyItems\LastEditedTimePropertyItem;
use Notion\Pages\PropertyItems\MultiSelectPropertyItem;
use Notion\Pages\PropertyItems\NumberPropertyItem;
use Notion\Pages\PropertyItems\PeoplePropertyItem;
use Notion\Pages\PropertyItems\PhoneNumberPropertyItem;
use Notion\Pages\PropertyItems\PropertyItemFactory;
use Notion\Pages\PropertyItems\PropertyItemList;
use Notion\Pages\PropertyItems\RelationPropertyItem;
use Notion\Pages\PropertyItems\RichTextPropertyItem;
use Notion\Pages\PropertyItems\RollupPropertyItem;
use Notion\Pages\PropertyItems\SelectPropertyItem;
use Notion\Pages\PropertyItems\StatusPropertyItem;
use Notion\Pages\PropertyItems\TitlePropertyItem;
use Notion\Pages\PropertyItems\UniqueIdPropertyItem;
use Notion\Pages\PropertyItems\UnknownPropertyItem;
use Notion\Pages\PropertyItems\UrlPropertyItem;
use PHPUnit\Framework\TestCase;

class PropertyItemFactoryTest extends TestCase
{
    public function test_list(): void
    {
        $array = [
            "object" => "list",
            "results" => [],
            "next_cursor" => null,
            "has_more" => false,
            "type" => "property_item",
            "property_item" => [
                "id" => "title",
                "type" => "title",
                "next_url" => null,
            ],
        ];

        $res = PropertyItemFactory::fromArray($array);
        $this->assertInstanceOf(PropertyItemList::class, $res);
    }

    public function test_all_property_item_types(): void
    {
        $types = [
            "title" => [
                "array" => [
                    "object" => "property_item", "id" => "1", "type" => "title",
                    "title" => ["plain_text" => "t", "href" => null, "annotations" => [
                        "bold" => false, "italic" => false, "strikethrough" => false,
                        "underline" => false, "code" => false, "color" => "default",
                    ], "type" => "text", "text" => ["content" => "t"]],
                ],
                "expected" => TitlePropertyItem::class,
            ],
            "rich_text" => [
                "array" => [
                    "object" => "property_item", "id" => "2", "type" => "rich_text",
                    "rich_text" => ["plain_text" => "r", "href" => null, "annotations" => [
                        "bold" => false, "italic" => false, "strikethrough" => false,
                        "underline" => false, "code" => false, "color" => "default",
                    ], "type" => "text", "text" => ["content" => "r"]],
                ],
                "expected" => RichTextPropertyItem::class,
            ],
            "number" => [
                "array" => ["object" => "property_item", "id" => "3", "type" => "number", "number" => 123],
                "expected" => NumberPropertyItem::class,
            ],
            "select" => [
                "array" => ["object" => "property_item", "id" => "4", "type" => "select", "select" => null],
                "expected" => SelectPropertyItem::class,
            ],
            "multi_select" => [
                "array" => [
                    "object" => "property_item", "id" => "5", "type" => "multi_select",
                    "multi_select" => [],
                ],
                "expected" => MultiSelectPropertyItem::class,
            ],
            "date" => [
                "array" => ["object" => "property_item", "id" => "6", "type" => "date", "date" => null],
                "expected" => DatePropertyItem::class,
            ],
            "formula" => [
                "array" => [
                    "object" => "property_item", "id" => "7", "type" => "formula",
                    "formula" => ["type" => "number", "number" => 5],
                ],
                "expected" => FormulaPropertyItem::class,
            ],
            "relation" => [
                "array" => [
                    "object" => "property_item", "id" => "8", "type" => "relation",
                    "relation" => ["id" => "page-id"],
                ],
                "expected" => RelationPropertyItem::class,
            ],
            "rollup" => [
                "array" => [
                    "object" => "property_item", "id" => "9", "type" => "rollup",
                    "rollup" => ["type" => "number", "function" => "count", "number" => 1],
                ],
                "expected" => RollupPropertyItem::class,
            ],
            "people" => [
                "array" => [
                    "object" => "property_item", "id" => "10", "type" => "people",
                    "people" => [
                        "object" => "user", "id" => "u1", "name" => "User", "avatar_url" => null,
                        "type" => "person", "person" => ["email" => "u@test.com"],
                    ],
                ],
                "expected" => PeoplePropertyItem::class,
            ],
            "files" => [
                "array" => ["object" => "property_item", "id" => "11", "type" => "files", "files" => []],
                "expected" => FilesPropertyItem::class,
            ],
            "checkbox" => [
                "array" => ["object" => "property_item", "id" => "12", "type" => "checkbox", "checkbox" => true],
                "expected" => CheckboxPropertyItem::class,
            ],
            "url" => [
                "array" => ["object" => "property_item", "id" => "13", "type" => "url", "url" => "https://test.com"],
                "expected" => UrlPropertyItem::class,
            ],
            "email" => [
                "array" => ["object" => "property_item", "id" => "14", "type" => "email", "email" => "a@b.com"],
                "expected" => EmailPropertyItem::class,
            ],
            "phone_number" => [
                "array" => [
                    "object" => "property_item", "id" => "15", "type" => "phone_number",
                    "phone_number" => "123",
                ],
                "expected" => PhoneNumberPropertyItem::class,
            ],
            "created_time" => [
                "array" => [
                    "object" => "property_item", "id" => "16", "type" => "created_time",
                    "created_time" => "2020-03-17T19:10:04.968Z",
                ],
                "expected" => CreatedTimePropertyItem::class,
            ],
            "created_by" => [
                "array" => [
                    "object" => "property_item", "id" => "17", "type" => "created_by",
                    "created_by" => [
                        "object" => "user", "id" => "u2", "name" => "Creator", "avatar_url" => null,
                        "type" => "person", "person" => ["email" => "c@test.com"],
                    ],
                ],
                "expected" => CreatedByPropertyItem::class,
            ],
            "last_edited_time" => [
                "array" => [
                    "object" => "property_item", "id" => "18", "type" => "last_edited_time",
                    "last_edited_time" => "2020-03-17T19:10:04.968Z",
                ],
                "expected" => LastEditedTimePropertyItem::class,
            ],
            "last_edited_by" => [
                "array" => [
                    "object" => "property_item", "id" => "19", "type" => "last_edited_by",
                    "last_edited_by" => [
                        "object" => "user", "id" => "u3", "name" => "Editor", "avatar_url" => null,
                        "type" => "person", "person" => ["email" => "e@test.com"],
                    ],
                ],
                "expected" => LastEditedByPropertyItem::class,
            ],
            "status" => [
                "array" => [
                    "object" => "property_item", "id" => "20", "type" => "status",
                    "status" => null,
                ],
                "expected" => StatusPropertyItem::class,
            ],
            "unique_id" => [
                "array" => [
                    "object" => "property_item", "id" => "21", "type" => "unique_id",
                    "unique_id" => ["number" => 1, "prefix" => "A"],
                ],
                "expected" => UniqueIdPropertyItem::class,
            ],
            "unknown" => [
                "array" => ["object" => "property_item", "id" => "22", "type" => "unsupported_type"],
                "expected" => UnknownPropertyItem::class,
            ],
        ];

        foreach ($types as $type => $info) {
            $parsed = PropertyItemFactory::fromArray($info["array"]);
            /** @var class-string $expected */
            $expected = $info["expected"];
            $this->assertInstanceOf($expected, $parsed, "Failed for type: {$type}");
        }
    }
}
