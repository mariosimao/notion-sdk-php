<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Common\RichText;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\PropertyItemList;
use Notion\Pages\PropertyItems\PropertyItemMetadata;
use Notion\Pages\PropertyItems\RelationPropertyItem;
use Notion\Pages\PropertyItems\TitlePropertyItem;
use PHPUnit\Framework\TestCase;

class PropertyItemListTest extends TestCase
{
    public function test_title_property_item_list(): void
    {
        $array = [
            "object" => "list",
            "results" => [
                [
                    "object" => "property_item",
                    "id" => "title",
                    "type" => "title",
                    "title" => [
                        "plain_text" => "Page Title",
                        "href" => null,
                        "annotations" => [
                            "bold" => false,
                            "italic" => false,
                            "strikethrough" => false,
                            "underline" => false,
                            "code" => false,
                            "color" => "default",
                        ],
                        "type" => "text",
                        "text" => ["content" => "Page Title"],
                    ],
                ],
            ],
            "next_cursor" => null,
            "has_more" => false,
            "type" => "property_item",
            "property_item" => [
                "id" => "title",
                "next_url" => null,
                "type" => "title",
            ],
        ];

        $list = PropertyItemList::fromArray($array);

        $this->assertCount(1, $list->results);
        $this->assertInstanceOf(TitlePropertyItem::class, $list->results[0]);
        $this->assertSame("title", $list->id());
        $this->assertSame(PropertyType::Title, $list->type());
        $this->assertNull($list->nextCursor);
        $this->assertFalse($list->hasMore);
        $this->assertNull($list->nextUrl());
        $this->assertTrue($list->isTitle());
        $this->assertFalse($list->isRichText());
        $this->assertFalse($list->isRelation());
        $this->assertFalse($list->isPeople());
        $this->assertFalse($list->isRollup());

        $expected = $array;
        $expected["property_item"] = [
            "object" => "property_item",
            "id" => "title",
            "type" => "title",
        ];
        $this->assertEquals($expected, $list->toArray());
    }

    public function test_relation_property_item_list_paginated(): void
    {
        $nextUrl = "http://api.notion.com/v1/pages/0e5235bf86aa4efb93aa772cce7eab71/properties/vYdV?start_cursor=123";
        $array = [
            "object" => "list",
            "results" => [
                [
                    "object" => "property_item",
                    "id" => "vYdV",
                    "type" => "relation",
                    "relation" => [
                        "id" => "535c3fb2-95e6-4b37-a696-036e5eac5cf6",
                    ],
                ],
            ],
            "next_cursor" => "next-cursor-123",
            "has_more" => true,
            "type" => "property_item",
            "property_item" => [
                "id" => "vYdV",
                "next_url" => $nextUrl,
                "type" => "relation",
            ],
        ];

        $list = PropertyItemList::fromArray($array);

        $this->assertSame("next-cursor-123", $list->nextCursor);
        $this->assertTrue($list->hasMore);
        $this->assertSame($nextUrl, $list->nextUrl());
        $this->assertInstanceOf(RelationPropertyItem::class, $list->results[0]);
        $this->assertSame("535c3fb2-95e6-4b37-a696-036e5eac5cf6", $list->results[0]->pageId);
    }

    public function test_create(): void
    {
        $rel = RelationPropertyItem::create("page-123", "rel-1");
        $meta = PropertyItemMetadata::create("rel-1", PropertyType::Relation);
        $list = PropertyItemList::create([$rel], "cursor-xyz", false, $meta);

        $this->assertSame("cursor-xyz", $list->nextCursor);
        $this->assertFalse($list->hasMore);
        $this->assertCount(1, $list->results);
        $this->assertTrue($list->isRelation());
    }
}
