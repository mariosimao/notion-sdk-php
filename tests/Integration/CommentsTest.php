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
    public function test_create_and_read_page_comment(): void
    {
        $notion = Helper::client();
        $page = Helper::newPage()->changeTitle("Comments test");

        $page = $notion->pages()->create($page);

        $comment = Comment::create($page->id, RichText::fromString("Sample comment"));
        $comment = $notion->comments()->create($comment);

        $comments = $notion->comments()->list($page->id);

        $this->assertSame($comment->id, $comments[0]->id);
        $this->assertSame("Sample comment", $comments[0]->text[0]->toString());

        $notion->pages()->delete($page);
    }
}
