<?php

namespace Notion\Test\Unit\Blocks;

use Exception;
use Notion\Blocks\BlockFactory;
use Notion\Blocks\BlockType;
use Notion\Blocks\LinkToPage;
use Notion\Blocks\LinkToPageType;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use PHPUnit\Framework\TestCase;

class LinkToPageTest extends TestCase
{
    public function test_create_page(): void
    {
        $block = LinkToPage::page("61cca5bd-c8c6-4fcc-b517-514da3b8b1e0");

        $this->assertSame(BlockType::LinkToPage, $block->metadata()->type);
        $this->assertSame(LinkToPageType::Page, $block->type);
        $this->assertSame("61cca5bd-c8c6-4fcc-b517-514da3b8b1e0", $block->pageId);
        $this->assertNull($block->databaseId);
        $this->assertNull($block->commentId);
        $this->assertSame("61cca5bd-c8c6-4fcc-b517-514da3b8b1e0", $block->targetId());
        $this->assertTrue($block->isPage());
        $this->assertFalse($block->isDatabase());
        $this->assertFalse($block->isComment());
    }

    public function test_create_database(): void
    {
        $block = LinkToPage::database("d9824bdc-8445-4327-be8b-5b47500af6ce");

        $this->assertSame(BlockType::LinkToPage, $block->metadata()->type);
        $this->assertSame(LinkToPageType::Database, $block->type);
        $this->assertNull($block->pageId);
        $this->assertSame("d9824bdc-8445-4327-be8b-5b47500af6ce", $block->databaseId);
        $this->assertNull($block->commentId);
        $this->assertSame("d9824bdc-8445-4327-be8b-5b47500af6ce", $block->targetId());
        $this->assertFalse($block->isPage());
        $this->assertTrue($block->isDatabase());
        $this->assertFalse($block->isComment());
    }

    public function test_create_comment(): void
    {
        $block = LinkToPage::comment("b530263f-6772-469b-9c71-f9256eb91000");

        $this->assertSame(BlockType::LinkToPage, $block->metadata()->type);
        $this->assertSame(LinkToPageType::Comment, $block->type);
        $this->assertNull($block->pageId);
        $this->assertNull($block->databaseId);
        $this->assertSame("b530263f-6772-469b-9c71-f9256eb91000", $block->commentId);
        $this->assertSame("b530263f-6772-469b-9c71-f9256eb91000", $block->targetId());
        $this->assertFalse($block->isPage());
        $this->assertFalse($block->isDatabase());
        $this->assertTrue($block->isComment());
    }

    public function test_create_helper(): void
    {
        $page = LinkToPage::create("page-123", LinkToPageType::Page);
        $this->assertTrue($page->isPage());
        $this->assertSame("page-123", $page->pageId);

        $db = LinkToPage::create("db-123", LinkToPageType::Database);
        $this->assertTrue($db->isDatabase());
        $this->assertSame("db-123", $db->databaseId);

        $comment = LinkToPage::create("comment-123", LinkToPageType::Comment);
        $this->assertTrue($comment->isComment());
        $this->assertSame("comment-123", $comment->commentId);
    }

    public function test_array_conversion_page(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "link_to_page",
            "link_to_page"     => [
                "type"    => "page_id",
                "page_id" => "61cca5bd-c8c6-4fcc-b517-514da3b8b1e0",
            ],
        ];

        $block = LinkToPage::fromArray($array);

        $this->assertEquals($array, $block->toArray());
        $this->assertSame("61cca5bd-c8c6-4fcc-b517-514da3b8b1e0", $block->pageId);
        $this->assertTrue($block->isPage());
    }

    public function test_array_conversion_database(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "link_to_page",
            "link_to_page"     => [
                "type"        => "database_id",
                "database_id" => "d9824bdc-8445-4327-be8b-5b47500af6ce",
            ],
        ];

        $block = LinkToPage::fromArray($array);

        $this->assertEquals($array, $block->toArray());
        $this->assertSame("d9824bdc-8445-4327-be8b-5b47500af6ce", $block->databaseId);
        $this->assertTrue($block->isDatabase());
    }

    public function test_array_conversion_comment(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "link_to_page",
            "link_to_page"     => [
                "type"       => "comment_id",
                "comment_id" => "b530263f-6772-469b-9c71-f9256eb91000",
            ],
        ];

        $block = LinkToPage::fromArray($array);

        $this->assertEquals($array, $block->toArray());
        $this->assertSame("b530263f-6772-469b-9c71-f9256eb91000", $block->commentId);
        $this->assertTrue($block->isComment());
    }

    public function test_from_array_without_type(): void
    {
        $pageArray = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "link_to_page",
            "link_to_page"     => [
                "page_id" => "61cca5bd-c8c6-4fcc-b517-514da3b8b1e0",
            ],
        ];
        $block = LinkToPage::fromArray($pageArray);
        $this->assertTrue($block->isPage());
        $this->assertSame("61cca5bd-c8c6-4fcc-b517-514da3b8b1e0", $block->pageId);

        $dbArray = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "link_to_page",
            "link_to_page"     => [
                "database_id" => "d9824bdc-8445-4327-be8b-5b47500af6ce",
            ],
        ];
        $dbBlock = LinkToPage::fromArray($dbArray);
        $this->assertTrue($dbBlock->isDatabase());
        $this->assertSame("d9824bdc-8445-4327-be8b-5b47500af6ce", $dbBlock->databaseId);

        $commentArray = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "link_to_page",
            "link_to_page"     => [
                "comment_id" => "b530263f-6772-469b-9c71-f9256eb91000",
            ],
        ];
        $commentBlock = LinkToPage::fromArray($commentArray);
        $this->assertTrue($commentBlock->isComment());
        $this->assertSame("b530263f-6772-469b-9c71-f9256eb91000", $commentBlock->commentId);
    }

    public function test_from_invalid_type(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "paragraph",
            "link_to_page"     => [
                "type"    => "page_id",
                "page_id" => "61cca5bd-c8c6-4fcc-b517-514da3b8b1e0",
            ],
        ];

        $this->expectException(Exception::class);
        LinkToPage::fromArray($array);
    }

    public function test_from_array_invalid_payload(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "link_to_page",
            "link_to_page"     => [],
        ];

        $this->expectException(Exception::class);
        LinkToPage::fromArray($array);
    }

    public function test_block_factory(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "link_to_page",
            "link_to_page"     => [
                "type"    => "page_id",
                "page_id" => "61cca5bd-c8c6-4fcc-b517-514da3b8b1e0",
            ],
        ];

        $block = BlockFactory::fromArray($array);

        $this->assertInstanceOf(LinkToPage::class, $block);
    }

    public function test_change_page(): void
    {
        $block = LinkToPage::database("db-123");
        $newBlock = $block->changePage("page-456");

        $this->assertTrue($newBlock->isPage());
        $this->assertSame("page-456", $newBlock->pageId);
    }

    public function test_change_database(): void
    {
        $block = LinkToPage::page("page-123");
        $newBlock = $block->changeDatabase("db-456");

        $this->assertTrue($newBlock->isDatabase());
        $this->assertSame("db-456", $newBlock->databaseId);
    }

    public function test_change_comment(): void
    {
        $block = LinkToPage::page("page-123");
        $newBlock = $block->changeComment("comment-456");

        $this->assertTrue($newBlock->isComment());
        $this->assertSame("comment-456", $newBlock->commentId);
    }

    public function test_no_children_support(): void
    {
        $block = LinkToPage::page("page-123");

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_add_child(): void
    {
        $block = LinkToPage::page("page-123");

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_delete(): void
    {
        $block = LinkToPage::page("page-123");
        $deleted = $block->delete();

        $this->assertInstanceOf(LinkToPage::class, $deleted);
        $this->assertTrue($deleted->metadata()->inTrash);
        $this->assertSame($block->type, $deleted->type);
        $this->assertSame($block->pageId, $deleted->pageId);
    }

    public function test_archive(): void
    {
        $block = LinkToPage::page("page-123");
        /** @psalm-suppress DeprecatedMethod */
        $archived = $block->archive();

        $this->assertTrue($archived->metadata()->inTrash);
    }
}
