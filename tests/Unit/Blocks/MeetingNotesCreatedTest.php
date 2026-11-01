<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\MeetingNotesCreated;
use PHPUnit\Framework\TestCase;

class MeetingNotesCreatedTest extends TestCase
{
    public function test_partial_response(): void
    {
        $created = MeetingNotesCreated::fromArray([
            "object" => "block",
            "id"     => "04a13895-f072-4814-8af7-cd11af127040",
        ]);

        $this->assertSame("04a13895-f072-4814-8af7-cd11af127040", $created->id);
        $this->assertNull($created->block);
    }

    public function test_full_response(): void
    {
        $created = MeetingNotesCreated::fromArray([
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "type"             => "meeting_notes",
            "meeting_notes"    => [
                "is_full_page" => false,
                "title"        => [],
                "status"       => "transcription_not_started",
            ],
            "created_time"     => "2026-03-11T17:09:00.000Z",
            "last_edited_time" => "2026-03-11T17:09:00.000Z",
            "created_by"       => [ "object" => "user", "id" => "6794760a-1f15-45cd-9c65-0dfe42f5135a" ],
            "last_edited_by"   => [ "object" => "user", "id" => "6794760a-1f15-45cd-9c65-0dfe42f5135a" ],
            "has_children"     => false,
            "in_trash"         => false,
        ]);

        $this->assertSame("04a13895-f072-4814-8af7-cd11af127040", $created->id);
        $this->assertNotNull($created->block);
        $this->assertSame("04a13895-f072-4814-8af7-cd11af127040", $created->block->metadata()->id);
    }
}
