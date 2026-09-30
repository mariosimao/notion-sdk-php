<?php

namespace Notion\Test\Unit\Pages\Markdown;

use Notion\Pages\Markdown\ContentUpdate;
use Notion\Pages\Markdown\InsertContent;
use Notion\Pages\Markdown\InsertPosition;
use Notion\Pages\Markdown\PageMarkdown;
use Notion\Pages\Markdown\ReplaceContent;
use Notion\Pages\Markdown\ReplaceContentRange;
use Notion\Pages\Markdown\UpdateContent;
use PHPUnit\Framework\TestCase;

class MarkdownTest extends TestCase
{
    public function test_page_markdown_from_array(): void
    {
        $markdown = PageMarkdown::fromArray([
            "object" => "page_markdown",
            "id" => "a7e1c2a0-0d1b-4f5c-9a3e-3c1b2a0d1b4f",
            "markdown" => "# Title\n\nParagraph",
            "truncated" => true,
            "unknown_block_ids" => [ "0b0f5b8e-2d7c-4d8e-9a1b-6f3e8c1d2a4b" ],
        ]);

        $this->assertSame("a7e1c2a0-0d1b-4f5c-9a3e-3c1b2a0d1b4f", $markdown->id);
        $this->assertSame("# Title\n\nParagraph", $markdown->markdown);
        $this->assertTrue($markdown->truncated);
        $this->assertSame([ "0b0f5b8e-2d7c-4d8e-9a1b-6f3e8c1d2a4b" ], $markdown->unknownBlockIds);
    }

    public function test_insert_content_append(): void
    {
        $update = InsertContent::create("Hello");

        $this->assertNull($update->after);
        $this->assertNull($update->position);
        $this->assertSame([
            "type" => "insert_content",
            "insert_content" => [ "content" => "Hello" ],
        ], $update->toArray());
    }

    public function test_insert_content_at_start(): void
    {
        $update = InsertContent::atStart("Hello");

        $this->assertSame(InsertPosition::Start, $update->position);
        $this->assertSame([
            "type" => "insert_content",
            "insert_content" => [ "content" => "Hello", "position" => [ "type" => "start" ] ],
        ], $update->toArray());
    }

    public function test_insert_content_at_end(): void
    {
        $update = InsertContent::atEnd("Hello");

        $this->assertSame(InsertPosition::End, $update->position);
        $this->assertSame([
            "type" => "insert_content",
            "insert_content" => [ "content" => "Hello", "position" => [ "type" => "end" ] ],
        ], $update->toArray());
    }

    public function test_insert_content_after_selection(): void
    {
        $update = InsertContent::after("Intro...end", "Hello");

        $this->assertSame([
            "type" => "insert_content",
            "insert_content" => [ "content" => "Hello", "after" => "Intro...end" ],
        ], $update->toArray());
    }

    public function test_replace_content_range(): void
    {
        $update = ReplaceContentRange::create("Old...text", "New text");

        $this->assertFalse($update->allowDeletingContent);
        $this->assertSame([
            "type" => "replace_content_range",
            "replace_content_range" => [
                "content" => "New text",
                "content_range" => "Old...text",
                "allow_deleting_content" => false,
            ],
        ], $update->toArray());

        $this->assertTrue($update->allowDeletingContent()->allowDeletingContent);
    }

    public function test_update_content(): void
    {
        $update = UpdateContent::create(ContentUpdate::create("foo", "bar"))
            ->addUpdate(ContentUpdate::create("baz", "qux")->replaceAllMatches())
            ->allowDeletingContent();

        $this->assertCount(2, $update->contentUpdates);
        $this->assertSame([
            "type" => "update_content",
            "update_content" => [
                "content_updates" => [
                    [ "old_str" => "foo", "new_str" => "bar", "replace_all_matches" => false ],
                    [ "old_str" => "baz", "new_str" => "qux", "replace_all_matches" => true ],
                ],
                "allow_deleting_content" => true,
            ],
        ], $update->toArray());
    }

    public function test_replace_content(): void
    {
        $update = ReplaceContent::create("# New")->allowDeletingContent();

        $this->assertSame([
            "type" => "replace_content",
            "replace_content" => [
                "new_str" => "# New",
                "allow_deleting_content" => true,
            ],
        ], $update->toArray());

        $this->assertFalse($update->allowDeletingContent(false)->allowDeletingContent);
    }
}
