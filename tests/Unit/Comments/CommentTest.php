<?php

namespace Notion\Test\Unit\Comments;

use Notion\Comments\Comment;
use Notion\Comments\CommentAttachment;
use Notion\Comments\CommentAttachmentCategory;
use Notion\Comments\CommentDisplayName;
use Notion\Comments\CommentDisplayNameType;
use Notion\Common\ParentType;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function test_create_page_comment(): void
    {
        $pageId = "8e2b2ad4-63f6-4e9c-8036-9a19c8d3c896";
        $comment = Comment::create($pageId, RichText::fromString("Sample comment"));

        $this->assertNull($comment->discussionId);
        $this->assertSame($pageId, $comment->parent?->id);
        $this->assertSame(ParentType::Page, $comment->parent?->type);
        $this->assertSame("Sample comment", RichText::multipleToString(...$comment->text));
    }

    public function test_create_disscussion_comment(): void
    {
        $discussionId = "27948296-9e5f-4c26-8fc5-46bea548cc33";
        $comment = Comment::createReply($discussionId, RichText::fromString("Sample comment"));

        $this->assertNull($comment->parent);
        $this->assertSame($discussionId, $comment->discussionId);
        $this->assertSame("Sample comment", RichText::multipleToString(...$comment->text));
    }

    public function test_array_conversion_parent(): void
    {
        $array = [
            "id" => "9d85f9a4-bd50-4fab-8c27-a3b7f5167acf",
            "parent" => [
                "type" => "page_id",
                "page_id" => "d30f32b9-0bfd-419a-bc8a-3e27397a8efe",
            ],
            "created_time" => "2022-07-15T21:17:00.000000Z",
            "last_edited_time" => "2022-07-15T21:17:00.000000Z",
            "created_by" => [
                "object" => "user",
                "id" => "9ea7e392-1ac8-4bf6-86c9-7379e16ae94c",
            ],
            "rich_text" => [
                [
                    "plain_text"  => "Sample comment",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Sample comment",
                    ],
                    "annotations" => [
                        "bold"          => false,
                        "italic"        => false,
                        "strikethrough" => false,
                        "underline"     => false,
                        "code"          => false,
                        "color"         => "default",
                    ],
                ],
            ],
            "attachments" => [],
            "has_pending_attachments" => false,
        ];

        $comment = Comment::fromArray($array);
        $this->assertEquals($array, $comment->toArray());
    }

    public function test_array_conversion_discussion(): void
    {
        $array = [
            "id" => "9d85f9a4-bd50-4fab-8c27-a3b7f5167acf",
            "discussion_id" => "b341338f-b014-4f49-8ea6-459e9780ed64",
            "created_time" => "2022-07-15T21:17:00.000000Z",
            "last_edited_time" => "2022-07-15T21:17:00.000000Z",
            "created_by" => [
                "object" => "user",
                "id" => "9ea7e392-1ac8-4bf6-86c9-7379e16ae94c",
            ],
            "rich_text" => [
                [
                    "plain_text"  => "Sample comment",
                    "href"        => null,
                    "type"        => "text",
                    "text"        => [
                        "content" => "Sample comment",
                    ],
                    "annotations" => [
                        "bold"          => false,
                        "italic"        => false,
                        "strikethrough" => false,
                        "underline"     => false,
                        "code"          => false,
                        "color"         => "default",
                    ],
                ],
            ],
            "attachments" => [],
            "has_pending_attachments" => false,
        ];

        $comment = Comment::fromArray($array);
        $this->assertEquals($array, $comment->toArray());
    }

    public function test_array_conversion_attachments_and_display_name(): void
    {
        $array = [
            "id" => "9d85f9a4-bd50-4fab-8c27-a3b7f5167acf",
            "discussion_id" => "b341338f-b014-4f49-8ea6-459e9780ed64",
            "created_time" => "2022-07-15T21:17:00.000000Z",
            "last_edited_time" => "2022-07-15T21:17:00.000000Z",
            "created_by" => [
                "object" => "user",
                "id" => "9ea7e392-1ac8-4bf6-86c9-7379e16ae94c",
            ],
            "rich_text" => [],
            "attachments" => [
                [
                    "category" => "image",
                    "file" => [
                        "url" => "https://s3.us-west-2.amazonaws.com/image.png",
                        "expiry_time" => "2025-06-10T21:58:51.599000Z",
                    ],
                ],
            ],
            "display_name" => [
                "type" => "custom",
                "resolved_name" => "Notion Bot",
            ],
            "has_pending_attachments" => true,
        ];

        $comment = Comment::fromArray($array);

        $this->assertCount(1, $comment->attachments);
        $this->assertSame(CommentAttachmentCategory::Image, $comment->attachments[0]->category);
        $this->assertSame("https://s3.us-west-2.amazonaws.com/image.png", $comment->attachments[0]->file->url);
        $this->assertTrue($comment->attachments[0]->file->isInternal());
        $this->assertSame(CommentDisplayNameType::Custom, $comment->displayName?->type);
        $this->assertSame("Notion Bot", $comment->displayName?->resolvedName);
        $this->assertTrue($comment->hasPendingAttachments);
        $this->assertEquals($array, $comment->toArray());
    }

    public function test_missing_attachments_and_display_name(): void
    {
        $comment = Comment::fromArray([
            "id" => "9d85f9a4-bd50-4fab-8c27-a3b7f5167acf",
            "created_time" => "2022-07-15T21:17:00.000000Z",
            "last_edited_time" => "2022-07-15T21:17:00.000000Z",
            "created_by" => [ "object" => "user", "id" => "9ea7e392-1ac8-4bf6-86c9-7379e16ae94c" ],
            "rich_text" => [],
        ]);

        $this->assertSame([], $comment->attachments);
        $this->assertNull($comment->displayName);
        $this->assertFalse($comment->hasPendingAttachments);
    }

    public function test_change_attachments_and_display_name(): void
    {
        $comment = Comment::create("8e2b2ad4-63f6-4e9c-8036-9a19c8d3c896", RichText::fromString("Sample"))
            ->addAttachment(CommentAttachment::fromFileUpload("2e2cdb8b-9897-4a6c-a935-82922b1cfb87"))
            ->addAttachment(CommentAttachment::fromFileUpload("a5c2b1d0-9897-4a6c-a935-82922b1cfb87"))
            ->changeDisplayName(CommentDisplayName::custom("Notion Bot"));

        $this->assertCount(2, $comment->attachments);
        $this->assertTrue($comment->attachments[0]->isFileUpload());
        $this->assertSame("Notion Bot", $comment->displayName?->resolvedName);

        $comment = $comment->changeAttachments()->removeDisplayName();
        $this->assertSame([], $comment->attachments);
        $this->assertNull($comment->displayName);
    }
}
