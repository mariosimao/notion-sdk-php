<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Embed;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\Date;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class EmbedTest extends TestCase
{
    public function test_create_embed(): void
    {
        $embed = Embed::create("https://my-site.com");

        $this->assertEquals("https://my-site.com", $embed->url());
    }

    public function test_create_from_array(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "embed",
            "embed"            => [ "url" => "https://my-site.com" ],
        ];

        $embed = Embed::fromArray($array);

        $this->assertEquals("https://my-site.com", $embed->url());

        $this->assertEquals($embed, BlockFactory::fromArray($array));
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockTypeException::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "embed"            => [ "url" => "https://my-site.com" ],
        ];

        Embed::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $embed = Embed::create("https://my-site.com");

        $expected = [
            "object"           => "block",
            "created_time"     => $embed->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $embed->block()->createdTime()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "embed",
            "embed"            => [ "url" => "https://my-site.com" ],
        ];

        $this->assertEquals($expected, $embed->toArray());
    }

    public function test_replace_url(): void
    {
        $old = Embed::create("https://my-site.com");
        $new = $old->withUrl("https://another-site.com");

        $this->assertEquals("https://my-site.com", $old->url());
        $this->assertEquals("https://another-site.com", $new->url());
    }

    public function test_no_children_support(): void
    {
        $block = Embed::create();

        $this->expectException(NotionException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren([]);
    }

    public function test_array_for_update_operations(): void
    {
        $block = Embed::create();

        $array = $block->toUpdateArray();

        $this->assertCount(2, $array);
    }

    public function test_archive(): void
    {
        $block = Embed::create();

        $block = $block->archive();

        $this->assertTrue($block->block()->archived());
    }
}
