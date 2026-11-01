<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\MeetingNotesLanguage;
use Notion\Blocks\MeetingNotesRequest;
use Notion\Blocks\MeetingNotesSourceType;
use PHPUnit\Framework\TestCase;

class MeetingNotesRequestTest extends TestCase
{
    public function test_from_file_upload(): void
    {
        $request = MeetingNotesRequest::fromFileUpload("file-upload-id", "page-id");

        $this->assertSame(MeetingNotesSourceType::FileUpload, $request->sourceType);
        $this->assertSame("file-upload-id", $request->sourceId);
        $this->assertSame("page-id", $request->parentPageId);
        $this->assertNull($request->title);
        $this->assertNull($request->language);
        $this->assertNull($request->kickoffSummary);
        $this->assertSame([
            "source" => [ "type" => "file_upload", "file_upload_id" => "file-upload-id" ],
            "parent" => [ "type" => "page_id", "page_id" => "page-id" ],
        ], $request->toArray());
    }

    public function test_from_block(): void
    {
        $request = MeetingNotesRequest::fromBlock("block-id");

        $this->assertSame(MeetingNotesSourceType::Block, $request->sourceType);
        $this->assertSame("block-id", $request->sourceId);
        $this->assertNull($request->parentPageId);
        $this->assertSame([
            "source" => [ "type" => "block", "block_id" => "block-id" ],
        ], $request->toArray());
    }

    public function test_change_options(): void
    {
        $request = MeetingNotesRequest::fromFileUpload("file-upload-id", "page-id")
            ->changeTitle("Weekly sync")
            ->changeLanguage(MeetingNotesLanguage::English)
            ->enableSummary();

        $this->assertSame("Weekly sync", $request->title);
        $this->assertSame(MeetingNotesLanguage::English, $request->language);
        $this->assertTrue($request->kickoffSummary);
        $this->assertSame([
            "source"   => [ "type" => "file_upload", "file_upload_id" => "file-upload-id" ],
            "parent"   => [ "type" => "page_id", "page_id" => "page-id" ],
            "title"    => "Weekly sync",
            "language" => "en",
            "options"  => [ "kickoff_summary" => true ],
        ], $request->toArray());
    }

    public function test_disable_summary(): void
    {
        $request = MeetingNotesRequest::fromBlock("block-id")
            ->changeLanguage(MeetingNotesLanguage::Norwegian)
            ->disableSummary();

        $this->assertFalse($request->kickoffSummary);
        $this->assertSame([
            "source"   => [ "type" => "block", "block_id" => "block-id" ],
            "language" => "no",
            "options"  => [ "kickoff_summary" => false ],
        ], $request->toArray());
    }
}
