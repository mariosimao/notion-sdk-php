<?php

namespace Notion\Test\Integration;

use Notion\Comments\Comment;
use Notion\Comments\CommentAttachment;
use Notion\Comments\CommentDisplayName;
use Notion\Comments\CommentDisplayNameType;
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

    public function test_create_comment_with_custom_display_name(): void
    {
        $notion = Helper::client();
        $page = $notion->pages()->create(Helper::newPage()->changeTitle("Comment display name test"));

        $comment = Comment::create($page->id, RichText::fromString("Automated comment"))
            ->changeDisplayName(CommentDisplayName::custom("Notion Bot"));
        $comment = $notion->comments()->create($comment);

        $this->assertSame(CommentDisplayNameType::Custom, $comment->displayName?->type);
        $this->assertSame("Notion Bot", $comment->displayName?->resolvedName);

        $comments = $notion->comments()->list($page->id);
        $this->assertSame("Notion Bot", $comments[0]->displayName?->resolvedName);

        $notion->pages()->delete($page);
    }

    public function test_create_comment_with_attachment(): void
    {
        $notion = Helper::client();
        $page = $notion->pages()->create(Helper::newPage()->changeTitle("Comment attachment test"));

        $upload = $notion->fileUploads()->createSinglePart("comment-attachment.txt");
        $upload = $notion->fileUploads()->sendSinglePart($upload->id, "comment-attachment.txt", "Hello, Notion!");

        $comment = Comment::create($page->id, RichText::fromString("Comment with attachment"))
            ->addAttachment(CommentAttachment::fromFileUpload($upload->id));
        $comment = $notion->comments()->create($comment);

        $this->assertCount(1, $comment->attachments);
        $this->assertFalse($comment->attachments[0]->isFileUpload());
        $this->assertNotEmpty($comment->attachments[0]->file->url);

        $comments = $notion->comments()->list($page->id);
        $this->assertCount(1, $comments[0]->attachments);

        $notion->pages()->delete($page);
    }
}
