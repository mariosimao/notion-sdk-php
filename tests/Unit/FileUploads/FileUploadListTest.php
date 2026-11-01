<?php

namespace Notion\Test\Unit\FileUploads;

use Notion\FileUploads\FileUploadList;
use Notion\FileUploads\FileUploadStatus;
use PHPUnit\Framework\TestCase;

class FileUploadListTest extends TestCase
{
    public function test_from_array(): void
    {
        $list = FileUploadList::fromArray([
            "object"      => "list",
            "results"     => [
                [
                    "object"           => "file_upload",
                    "id"               => "b52b8ed6-e029-4707-a671-832549c09de3",
                    "created_time"     => "2025-03-15T20:53:00.000Z",
                    "last_edited_time" => "2025-03-15T20:57:00.000Z",
                    "expiry_time"      => null,
                    "status"           => "uploaded",
                    "filename"         => "image.png",
                    "content_type"     => "image/png",
                    "content_length"   => 25,
                ],
            ],
            "next_cursor" => "fe2cc560-036c-44cd-90e8-294d5a74cebc",
            "has_more"    => true,
            "type"        => "file_upload",
            "file_upload" => new \stdClass(),
        ]);

        $this->assertCount(1, $list->results);
        $this->assertSame("b52b8ed6-e029-4707-a671-832549c09de3", $list->results[0]->id);
        $this->assertSame(FileUploadStatus::Uploaded, $list->results[0]->status);
        $this->assertSame("fe2cc560-036c-44cd-90e8-294d5a74cebc", $list->nextCursor);
        $this->assertTrue($list->hasMore);
    }

    public function test_from_array_last_page(): void
    {
        $list = FileUploadList::fromArray([
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
        $list = FileUploadList::create([], "cursor", true);
        $this->assertSame("cursor", $list->nextCursor);
        $this->assertTrue($list->hasMore);

        $empty = FileUploadList::create([]);
        $this->assertSame([], $empty->results);
        $this->assertNull($empty->nextCursor);
        $this->assertFalse($empty->hasMore);
    }
}
