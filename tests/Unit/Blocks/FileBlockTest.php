<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Exceptions\BlockException;
use Notion\Blocks\FileBlock;
use Notion\Blocks\Paragraph;
use Notion\Common\Date;
use Notion\Common\File;
use PHPUnit\Framework\TestCase;

class FileBlockTest extends TestCase
{
    public function test_create_file(): void
    {
        $file = File::createExternal("https://my-site.com/file.doc");
        $fileBlock = FileBlock::fromFile($file);

        $this->assertEquals($file, $fileBlock->file());
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
            "type"             => "file",
            "file"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/file.doc"
                ],
            ],
        ];

        $fileBlock = FileBlock::fromArray($array);

        $this->assertEquals("https://my-site.com/file.doc", $fileBlock->file()->url);

        $this->assertEquals($fileBlock, BlockFactory::fromArray($array));
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
            "file"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/file.doc"
                ],
            ],
        ];

        FileBlock::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $file = File::createExternal("https://my-site.com/file.doc");
        $fileBlock = FileBlock::fromFile($file);

        $expected = [
            "object"           => "block",
            "created_time"     => $fileBlock->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $fileBlock->metadata()->createdTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "file",
            "file"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/file.doc"
                ],
            ],
        ];

        $this->assertEquals($expected, $fileBlock->toArray());
    }

    public function test_replace_file(): void
    {
        $file1 = File::createExternal("https://my-site.com/file1.doc");
        $file2 = File::createExternal("https://my-site.com/file2.doc");

        $old = FileBlock::fromFile($file1);
        $new = $old->changeFile($file2);

        $this->assertEquals($file1, $old->file());
        $this->assertEquals($file2, $new->file());
    }

    public function test_no_children_support(): void
    {
        $file = File::createExternal("https://my-site.com/file.doc");
        $block = FileBlock::fromFile($file);

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $file = File::createExternal("https://my-site.com/file.doc");
        $block = FileBlock::fromFile($file);

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_archive(): void
    {
        $file = File::createExternal("https://example.com/file.doc");
        $block = FileBlock::fromFile($file);

        $block = $block->archive();

        $this->assertTrue($block->metadata()->archived);
    }
}
