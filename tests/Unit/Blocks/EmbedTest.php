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
            "in_trash"         => false,
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
            "in_trash"         => false,
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
            "in_trash"         => false,
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

    public function test_create_from_file_upload(): void
    {
        $embed = Embed::fromFileUpload("43833259-72ae-404e-8441-b6577f3159b4");

        $this->assertNull($embed->url);
        $this->assertSame("43833259-72ae-404e-8441-b6577f3159b4", $embed->fileUploadId);
        $this->assertTrue($embed->isFileUpload());
    }

    public function test_file_upload_to_array(): void
    {
        $embed = Embed::fromFileUpload("43833259-72ae-404e-8441-b6577f3159b4");

        $expected = [
            "object"           => "block",
            "created_time"     => $embed->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $embed->metadata()->createdTime->format(Date::FORMAT),
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "embed",
            "embed"            => [
                "type"        => "file_upload",
                "file_upload" => [ "id" => "43833259-72ae-404e-8441-b6577f3159b4" ],
            ],
        ];

        $this->assertEquals($expected, $embed->toArray());
    }

    public function test_file_upload_from_array(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "embed",
            "embed"            => [
                "type"        => "file_upload",
                "file_upload" => [ "id" => "43833259-72ae-404e-8441-b6577f3159b4" ],
            ],
        ];

        $embed = Embed::fromArray($array);

        $this->assertNull($embed->url);
        $this->assertSame("43833259-72ae-404e-8441-b6577f3159b4", $embed->fileUploadId);
    }

    public function test_url_embed_is_not_file_upload(): void
    {
        $embed = Embed::fromUrl("https://my-site.com");

        $this->assertNull($embed->fileUploadId);
        $this->assertFalse($embed->isFileUpload());
    }

    public function test_change_file_upload(): void
    {
        $old = Embed::fromUrl("https://my-site.com");
        $new = $old->changeFileUpload("43833259-72ae-404e-8441-b6577f3159b4");

        $this->assertSame("https://my-site.com", $old->url);
        $this->assertNull($new->url);
        $this->assertSame("43833259-72ae-404e-8441-b6577f3159b4", $new->fileUploadId);
        $this->assertSame(
            [ "type" => "file_upload", "file_upload" => [ "id" => "43833259-72ae-404e-8441-b6577f3159b4" ] ],
            $new->toArray()["embed"],
        );
    }

    public function test_change_url_removes_file_upload(): void
    {
        $old = Embed::fromFileUpload("43833259-72ae-404e-8441-b6577f3159b4");
        $new = $old->changeUrl("https://my-site.com");

        $this->assertSame("https://my-site.com", $new->url);
        $this->assertNull($new->fileUploadId);
        $this->assertSame([ "url" => "https://my-site.com" ], $new->toArray()["embed"]);
    }

    public function test_delete_preserves_file_upload(): void
    {
        $block = Embed::fromFileUpload("43833259-72ae-404e-8441-b6577f3159b4")->delete();

        $this->assertTrue($block->metadata()->inTrash);
        $this->assertSame(
            [ "type" => "file_upload", "file_upload" => [ "id" => "43833259-72ae-404e-8441-b6577f3159b4" ] ],
            $block->toArray()["embed"],
        );
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

    public function test_delete(): void
    {
        $block = Embed::fromUrl();

        $block = $block->delete();

        $this->assertTrue($block->metadata()->inTrash);
    }
}
