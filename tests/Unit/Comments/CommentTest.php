<?php

namespace Notion\Test\Unit\Comments;

use Notion\Comments\Comment;
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
        ];

        $comment = Comment::fromArray($array);
        $this->assertEquals($array, $comment->toArray());
    }
}
