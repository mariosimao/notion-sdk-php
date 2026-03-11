<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Bookmark;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class BookmarkTest extends TestCase
{
    public function test_create_bookmark(): void
    {
        $bookmark = Bookmark::fromUrl("https://my-site.com");

        $this->assertEquals("https://my-site.com", $bookmark->url);
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
            "type"             => "bookmark",
            "bookmark"         => [ "url" => "https://my-site.com", "caption" => [] ],
        ];

        $bookmark = Bookmark::fromArray($array);

        $this->assertEquals("https://my-site.com", $bookmark->url);

        $this->assertEquals($bookmark, BlockFactory::fromArray($array));
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
            "bookmark"         => [ "url" => "https://my-site.com", "caption" => [] ],
        ];

        Bookmark::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $bookmark = Bookmark::fromUrl("https://my-site.com");

        $expected = [
            "object"           => "block",
            "created_time"     => $bookmark->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $bookmark->metadata()->createdTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "bookmark",
            "bookmark"         => [ "url" => "https://my-site.com", "caption" => [] ],
        ];

        $this->assertEquals($expected, $bookmark->toArray());
    }

    public function test_replace_url(): void
    {
        $old = Bookmark::fromUrl("https://my-site.com");
        $new = $old->changeUrl("https://another-site.com");

        $this->assertEquals("https://my-site.com", $old->url);
        $this->assertEquals("https://another-site.com", $new->url);
    }

    public function test_replace_caption(): void
    {
        $caption = [ RichText::fromString("Bookmark caption") ];
        $bookmark = Bookmark::fromUrl("https://my-site.com")->changeCaption(...$caption);

        $this->assertEquals($caption, $bookmark->caption);
    }

    public function test_no_children_support(): void
    {
        $bookmark = Bookmark::fromUrl("https://my-site.com");

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $bookmark->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $bookmark = Bookmark::fromUrl("https://my-site.com");

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $bookmark->addChild(Paragraph::create());
    }
}
