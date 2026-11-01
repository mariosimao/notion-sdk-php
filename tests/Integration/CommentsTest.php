<?php

namespace Notion\Test\Integration;

use Notion\Comments\Comment;
use Notion\Comments\CommentBody;
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

        $comment = Comment::create($page->id, CommentBody::richText(RichText::fromString("Sample comment")));
        $comment = $notion->comments()->create($comment);

        $comments = $notion->comments()->list($page->id);

        $this->assertSame($comment->id, $comments[0]->id);
        $this->assertSame("Sample comment", $comments[0]->body->richText[0]->toString());

        $notion->pages()->delete($page);
    }

    public function test_create_markdown_page_comment(): void
    {
        $notion = Helper::client();
        $page = Helper::newPage()->changeTitle("Markdown comments test");

        $page = $notion->pages()->create($page);

        $comment = Comment::create($page->id, CommentBody::markdown("**Bold** and *italic*"));
        $comment = $notion->comments()->create($comment);

        $text = $comment->body->richText;
        $this->assertSame("Bold and italic", RichText::multipleToString(...$text));
        $this->assertTrue($text[0]->annotations->isBold);

        $reply = Comment::createReply((string) $comment->discussionId, CommentBody::markdown("`reply`"));
        $reply = $notion->comments()->create($reply);

        $this->assertSame($comment->discussionId, $reply->discussionId);
        $this->assertTrue($reply->body->richText[0]->annotations->isCode);

        $notion->pages()->delete($page);
    }
}
