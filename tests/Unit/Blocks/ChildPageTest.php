<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\ChildPage;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\Date;
use Notion\Common\RichText;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class ChildPageTest extends TestCase
{
    public function test_create_empty_heading(): void
    {
        $heading = ChildPage::create();

        $this->assertEmpty($heading->pageTitle());
    }

    public function test_create_from_string(): void
    {
        $heading = ChildPage::fromString("Page title");

        $this->assertEquals("Page title", $heading->pageTitle());
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
            "type"             => "child_page",
            "child_page"       => [ "title" => "Page title" ],
        ];

        $childPage = ChildPage::fromArray($array);

        $this->assertEquals("Page title", $childPage->pageTitle());
        $this->assertFalse($childPage->block()->archived());

        $this->assertEquals($childPage, BlockFactory::fromArray($array));
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
            "child_page"       => [ "title" => "Wrong array" ],
        ];

        ChildPage::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $childPage = ChildPage::fromString("Page title");

        $expected = [
            "object"           => "block",
            "created_time"     => $childPage->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $childPage->block()->createdTime()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "child_page",
            "child_page"       => [ "title" => "Page title" ],
        ];

        $this->assertEquals($expected, $childPage->toArray());
    }

    public function test_replace_page_title(): void
    {
        $oldHeading = ChildPage::fromString("Page 1");

        $newHeading = $oldHeading->withPageTitle("Page 2");

        $this->assertEquals("Page 1", $oldHeading->pageTitle());
        $this->assertEquals("Page 2", $newHeading->pageTitle());
    }

    public function test_no_children_support(): void
    {
        $block = ChildPage::create();

        $this->expectException(NotionException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren([]);
    }

    public function test_array_for_update_operations(): void
    {
        $block = ChildPage::create();

        $array = $block->toUpdateArray();

        $this->assertCount(2, $array);
    }

    public function test_archive(): void
    {
        $block = ChildPage::create();

        $block = $block->archive();

        $this->assertTrue($block->block()->archived());
    }
}
