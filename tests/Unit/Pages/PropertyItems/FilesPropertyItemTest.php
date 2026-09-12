<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Common\File;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\FilesPropertyItem;
use PHPUnit\Framework\TestCase;

class FilesPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $file = File::createExternal("https://example.com/image.png")->changeName("image.png");
        $item = FilesPropertyItem::create([$file], "files-id");

        $this->assertSame("files-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Files, $item->metadata()->type);
        $this->assertCount(1, $item->files);
        $this->assertFalse($item->isEmpty());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "KpQq",
            "type" => "files",
            "files" => [
                [
                    "type" => "external",
                    "name" => "Space Wallpaper",
                    "external" => [
                        "url" => "https://website.domain/images/space.png",
                    ],
                ],
            ],
        ];

        $item = FilesPropertyItem::fromArray($array);

        $this->assertSame("KpQq", $item->metadata()->id);
        $this->assertCount(1, $item->files);
        $this->assertSame("Space Wallpaper", $item->files[0]->name);
        $this->assertEquals([
            "object" => "property_item",
            "id" => "KpQq",
            "type" => "files",
            "files" => [
                [
                    "type" => "external",
                    "name" => "Space Wallpaper",
                    "external" => [
                        "url" => "https://website.domain/images/space.png",
                    ],
                ],
            ],
        ], $item->toArray());
    }

    public function test_empty(): void
    {
        $item = FilesPropertyItem::create([]);
        $this->assertTrue($item->isEmpty());
    }
}
