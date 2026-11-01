<?php

namespace Notion\Test\Unit\Comments;

use Notion\Comments\CommentBody;
use Notion\Common\MarkdownVersion;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class CommentBodyTest extends TestCase
{
    public function test_rich_text_body(): void
    {
        $body = CommentBody::richText(RichText::fromString("Hello "), RichText::fromString("world"));

        $this->assertTrue($body->isRichText());
        $this->assertFalse($body->isMarkdown());
        $this->assertNull($body->markdown);
        $this->assertNull($body->markdownVersion);
        $this->assertSame("Hello world", RichText::multipleToString(...$body->richText));

        $this->assertEquals(
            ["rich_text" => array_map(fn (RichText $t) => $t->toArray(), $body->richText)],
            $body->toArray(),
        );
    }

    public function test_markdown_body_defaults_to_v2(): void
    {
        $body = CommentBody::markdown("**Bold** and `code`");

        $this->assertTrue($body->isMarkdown());
        $this->assertFalse($body->isRichText());
        $this->assertSame([], $body->richText);
        $this->assertSame(MarkdownVersion::V2, $body->markdownVersion);
        $this->assertSame(
            ["markdown" => "**Bold** and `code`", "markdown_version" => "v2"],
            $body->toArray(),
        );
    }

    public function test_markdown_body_with_version(): void
    {
        $body = CommentBody::markdown("*Italic*", MarkdownVersion::V1);

        $this->assertSame(
            ["markdown" => "*Italic*", "markdown_version" => "v1"],
            $body->toArray(),
        );
    }

    public function test_empty_markdown_is_markdown(): void
    {
        $body = CommentBody::markdown("");

        $this->assertTrue($body->isMarkdown());
        $this->assertSame("", $body->toArray()["markdown"] ?? null);
    }
}
