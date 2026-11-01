<?php

namespace Notion\Test\Unit\Comments;

use Notion\Comments\CommentAttachment;
use Notion\Comments\CommentAttachmentCategory;
use PHPUnit\Framework\TestCase;

class CommentAttachmentTest extends TestCase
{
    public function test_from_file_upload(): void
    {
        $attachment = CommentAttachment::fromFileUpload("2e2cdb8b-9897-4a6c-a935-82922b1cfb87");

        $this->assertTrue($attachment->isFileUpload());
        $this->assertNull($attachment->category);
        $this->assertSame("2e2cdb8b-9897-4a6c-a935-82922b1cfb87", $attachment->file->fileId);
        $this->assertSame([
            "type" => "file_upload",
            "file_upload_id" => "2e2cdb8b-9897-4a6c-a935-82922b1cfb87",
        ], $attachment->toArray());
    }

    public function test_file_upload_array_conversion(): void
    {
        $array = [
            "type" => "file_upload",
            "file_upload_id" => "2e2cdb8b-9897-4a6c-a935-82922b1cfb87",
        ];

        $this->assertSame($array, CommentAttachment::fromArray($array)->toArray());
    }

    public function test_response_array_conversion(): void
    {
        $array = [
            "category" => "video",
            "file" => [
                "url" => "https://s3.us-west-2.amazonaws.com/video.mp4",
                "expiry_time" => "2025-06-10T21:26:03.070000Z",
            ],
        ];

        $attachment = CommentAttachment::fromArray($array);

        $this->assertFalse($attachment->isFileUpload());
        $this->assertSame(CommentAttachmentCategory::Video, $attachment->category);
        $this->assertSame("https://s3.us-west-2.amazonaws.com/video.mp4", $attachment->file->url);
        $this->assertSame("2025-06-10", $attachment->file->expiryTime?->format("Y-m-d"));
        $this->assertSame($array, $attachment->toArray());
    }
}
