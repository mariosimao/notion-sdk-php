<?php

namespace Notion\Test\Unit\Comments;

use Notion\Comments\Comment;
use Notion\Comments\CommentList;
use Notion\Common\RichText;
use PHPUnit\Framework\TestCase;

class CommentListTest extends TestCase
{
    public function test_from_array(): void
    {
        $comment = [
            "id" => "9d85f9a4-bd50-4fab-8c27-a3b7f5167acf",
            "parent" => [
                "type" => "page_id",
                "page_id" => "d30f32b9-0bfd-419a-bc8a-3e27397a8efe",
            ],
            "discussion_id" => "311523ee-1e41-4cd1-b1d5-e4ee6f2c0bf8",
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
                    "text"        => [ "content" => "Sample comment" ],
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

        $list = CommentList::fromArray([
            "object"      => "list",
            "results"     => [ $comment ],
            "next_cursor" => "fe2cc560-036c-44cd-90e8-294d5a74cebc",
            "has_more"    => true,
            "type"        => "comment",
            "comment"     => new \stdClass(),
        ]);

        $this->assertCount(1, $list->results);
        $this->assertSame("9d85f9a4-bd50-4fab-8c27-a3b7f5167acf", $list->results[0]->id);
        $this->assertSame("Sample comment", $list->results[0]->text[0]->toString());
        $this->assertSame("fe2cc560-036c-44cd-90e8-294d5a74cebc", $list->nextCursor);
        $this->assertTrue($list->hasMore);
    }

    public function test_from_array_last_page(): void
    {
        $list = CommentList::fromArray([
            "results"     => [],
            "next_cursor" => null,
            "has_more"    => false,
        ]);

        $this->assertSame([], $list->results);
        $this->assertNull($list->nextCursor);
        $this->assertFalse($list->hasMore);
    }

    public function test_create(): void
    {
        $comment = Comment::create("d30f32b9-0bfd-419a-bc8a-3e27397a8efe", RichText::fromString("Hi"));
        $list = CommentList::create([ $comment ], "cursor", true);

        $this->assertSame([ $comment ], $list->results);
        $this->assertSame("cursor", $list->nextCursor);
        $this->assertTrue($list->hasMore);

        $empty = CommentList::create([]);
        $this->assertNull($empty->nextCursor);
        $this->assertFalse($empty->hasMore);
    }
}
