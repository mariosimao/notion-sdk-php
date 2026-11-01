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

        $this->assertSame($comment->id, $comments->results[0]->id);
        $this->assertSame("Sample comment", $comments->results[0]->text[0]->toString());
        $this->assertFalse($comments->hasMore);
        $this->assertNull($comments->nextCursor);

        $notion->pages()->delete($page);
    }

    public function test_paginate_comments(): void
    {
        $notion = Helper::client();
        $page = $notion->pages()->create(Helper::newPage()->changeTitle("Comments pagination test"));

        $first = $notion->comments()->create(Comment::create($page->id, RichText::fromString("First")));
        $second = $notion->comments()->create(Comment::create($page->id, RichText::fromString("Second")));

        $firstPage = $notion->comments()->list($page->id, pageSize: 1);
        $this->assertCount(1, $firstPage->results);
        $this->assertTrue($firstPage->hasMore);
        $this->assertNotNull($firstPage->nextCursor);
        $this->assertSame($first->id, $firstPage->results[0]->id);

        $secondPage = $notion->comments()->list($page->id, $firstPage->nextCursor, 1);
        $this->assertCount(1, $secondPage->results);
        $this->assertSame($second->id, $secondPage->results[0]->id);

        $all = $notion->comments()->findAll($page->id);
        $this->assertSame([ $first->id, $second->id ], array_map(fn(Comment $c) => $c->id, $all));

        $notion->pages()->delete($page);
    }
}
