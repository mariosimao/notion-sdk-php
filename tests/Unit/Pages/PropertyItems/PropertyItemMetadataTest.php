<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\PropertyItemMetadata;
use PHPUnit\Framework\TestCase;

class PropertyItemMetadataTest extends TestCase
{
    public function test_create(): void
    {
        $meta = PropertyItemMetadata::create("prop-id", PropertyType::Title, "http://example.com/next");

        $this->assertSame("prop-id", $meta->id);
        $this->assertSame(PropertyType::Title, $meta->type);
        $this->assertSame("http://example.com/next", $meta->nextUrl);
    }

    public function test_from_array(): void
    {
        $array = [
            "id" => "prop-123",
            "type" => "checkbox",
            "next_url" => null,
        ];

        $meta = PropertyItemMetadata::fromArray($array);

        $this->assertSame("prop-123", $meta->id);
        $this->assertSame(PropertyType::Checkbox, $meta->type);
        $this->assertNull($meta->nextUrl);
        $this->assertSame([
            "object" => "property_item",
            "id" => "prop-123",
            "type" => "checkbox",
        ], $meta->toArray());
    }

    public function test_unknown_type(): void
    {
        $array = [
            "id" => "unknown-id",
            "type" => "future_type",
            "next_url" => "http://example.com",
        ];

        $meta = PropertyItemMetadata::fromArray($array);

        $this->assertSame("unknown-id", $meta->id);
        $this->assertSame(PropertyType::Unknown, $meta->type);
        $this->assertSame([
            "object" => "property_item",
            "id" => "unknown-id",
            "type" => "future_type",
            "next_url" => "http://example.com",
        ], $meta->toArray());
    }
}
