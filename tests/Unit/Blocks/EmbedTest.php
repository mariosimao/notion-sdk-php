<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Embed;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;
use PHPUnit\Framework\TestCase;

class EmbedTest extends TestCase
{
    public function test_create_embed(): void
    {
        $embed = Embed::fromUrl("https://my-site.com");

        $this->assertEquals("https://my-site.com", $embed->url);
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

        $this->assertEquals("https://my-site.com", $embed->url);

        $this->assertEquals($embed, BlockFactory::fromArray($array));
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockException::class);
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
        $embed = Embed::fromUrl("https://my-site.com");

        $expected = [
            "object"           => "block",
            "created_time"     => $embed->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $embed->metadata()->createdTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "embed",
            "embed"            => [ "url" => "https://my-site.com" ],
        ];

        $this->assertEquals($expected, $embed->toArray());
    }

    public function test_replace_url(): void
    {
        $old = Embed::fromUrl("https://my-site.com");
        $new = $old->changeUrl("https://another-site.com");

        $this->assertEquals("https://my-site.com", $old->url);
        $this->assertEquals("https://another-site.com", $new->url);
    }

    public function test_no_children_support(): void
    {
        $block = Embed::fromUrl();

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $block = Embed::fromUrl();

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_archive(): void
    {
        $block = Embed::fromUrl();

        $block = $block->archive();

        $this->assertTrue($block->metadata()->archived);
    }
}
