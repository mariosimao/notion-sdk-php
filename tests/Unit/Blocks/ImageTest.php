<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Exceptions\BlockException;
use Notion\Blocks\Image;
use Notion\Blocks\Paragraph;
use Notion\Common\Date;
use Notion\Common\File;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function test_create_image(): void
    {
        $file = File::createExternal("https://my-site.com/image.png");
        $image = Image::fromFile($file);

        $this->assertEquals($file, $image->file);
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
            "type"             => "image",
            "image"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/image.png"
                ],
            ],
        ];

        $image = Image::fromArray($array);

        $this->assertEquals("https://my-site.com/image.png", $image->file->url);

        $this->assertEquals($image, BlockFactory::fromArray($array));
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
            "image"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/image.png"
                ],
            ],
        ];

        Image::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $file = File::createExternal("https://my-site.com/image.png");
        $image = Image::fromFile($file);

        $expected = [
            "object"           => "block",
            "created_time"     => $image->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $image->metadata()->createdTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "image",
            "image"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/image.png"
                ],
            ],
        ];

        $this->assertEquals($expected, $image->toArray());
    }

    public function test_replace_file(): void
    {
        $file1 = File::createExternal("https://my-site.com/image1.png");
        $file2 = File::createExternal("https://my-site.com/image2.png");

        $old = Image::fromFile($file1);
        $new = $old->changeFile($file2);

        $this->assertEquals($file1, $old->file);
        $this->assertEquals($file2, $new->file);
    }

    public function test_no_children_support(): void
    {
        $file = File::createExternal("https://my-site.com/image.png");
        $block = Image::fromFile($file);

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $file = File::createExternal("https://my-site.com/image.png");
        $block = Image::fromFile($file);

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_archive(): void
    {
        $file = File::createExternal("https://my-site.com/image.png");
        $block = Image::fromFile($file);

        $block = $block->archive();

        $this->assertTrue($block->metadata()->archived);
    }

    public function test_change_caption(): void
    {
        $file = File::createExternal("https://my-site.com/image.png");

        $caption = RichText::fromString("Sample caption");
        $block = Image::fromFile($file)->changeCaption($caption);

        $this->assertEquals([$caption], $block->file->caption);
    }
}
