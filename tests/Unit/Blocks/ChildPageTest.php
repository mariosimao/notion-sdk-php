<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\ChildPage;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;
use Notion\Common\RichText;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class ChildPageTest extends TestCase
{
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

        $this->assertEquals("Page title", $childPage->pageTitle);
        $this->assertFalse($childPage->metadata()->archived);

        $this->assertEquals($childPage, BlockFactory::fromArray($array));
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
            "child_page"       => [ "title" => "Wrong array" ],
        ];

        ChildPage::fromArray($array);
    }
}
