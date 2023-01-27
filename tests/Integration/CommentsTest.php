<?php

namespace Notion\Test\Integration;

use Notion\Comments\Comment;
use Notion\Common\RichText;
use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use PHPUnit\Framework\TestCase;

class CommentsTest extends TestCase
{
    private const DEFAULT_PARENT_ID = "3f4c46dee17f43b79587094b61407a31";

    /** @group test */
    public function test_create_and_read_page_comment(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $notion = Notion::create($token);

        $page = Page::create(PageParent::page(self::DEFAULT_PARENT_ID))
            ->changeTitle("Comments test");

        $page = $notion->pages()->create($page);

        $comment = Comment::create($page->id, RichText::fromString("Sample comment"));
        $comment = $notion->comments()->create($comment);

        $comments = $notion->comments()->list($page->id);

        $this->assertSame($comment->id, $comments[0]->id);
        $this->assertSame("Sample comment", $comments[0]->text[0]->toString());

        $notion->pages()->delete($page);
    }
}
