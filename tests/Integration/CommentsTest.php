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

    public function test_update_comment(): void
    {
        $notion = Helper::client();
        $page = Helper::newPage()->changeTitle("Update comment test");

        $page = $notion->pages()->create($page);

        $comment = Comment::create($page->id, RichText::fromString("Sample comment"));
        $comment = $notion->comments()->create($comment);

        $updated = $notion->comments()->update(
            $comment->changeText(RichText::fromString("Updated comment")),
        );

        $this->assertSame($comment->id, $updated->id);
        $this->assertSame("Updated comment", RichText::multipleToString(...$updated->text));

        $comments = $notion->comments()->list($page->id);
        $this->assertSame("Updated comment", RichText::multipleToString(...$comments[0]->text));

        $notion->pages()->delete($page);
    }

    public function test_delete_comment(): void
    {
        $notion = Helper::client();
        $page = Helper::newPage()->changeTitle("Delete comment test");

        $page = $notion->pages()->create($page);

        $comment = Comment::create($page->id, RichText::fromString("Sample comment"));
        $comment = $notion->comments()->create($comment);

        $deleted = $notion->comments()->delete($comment->id);

        $this->assertSame($comment->id, $deleted->id);
        $this->assertCount(0, $notion->comments()->list($page->id));

        $notion->pages()->delete($page);
    }
}
