<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\BlockType;
use Notion\Blocks\MeetingNotes;
use Notion\Blocks\MeetingNotes\Status;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use PHPUnit\Framework\TestCase;

class MeetingNotesTest extends TestCase
{
    public function test_create_from_array(): void
    {
        $array = $this->mockArray();

        $block = MeetingNotes::fromArray($array);

        $this->assertSame(BlockType::MeetingNotes, $block->metadata()->type);
        $this->assertTrue($block->isFullPage);
        $this->assertSame("Weekly sync", $block->toString());
        $this->assertSame(Status::NotesReady, $block->status);
        $this->assertSame("1d5a4e3c-0f4b-4d2b-8f2e-000000000001", $block->tabs?->summaryBlockId);
        $this->assertSame("1d5a4e3c-0f4b-4d2b-8f2e-000000000002", $block->tabs?->notesBlockId);
        $this->assertSame("1d5a4e3c-0f4b-4d2b-8f2e-000000000003", $block->tabs?->transcriptBlockId);
        $this->assertSame("2026-01-15", $block->calendarEvent?->startTime->format("Y-m-d"));
        $this->assertSame("event-123", $block->calendarEvent?->eventId);
        $this->assertSame(["3f2b25f3-0a52-4a90-8f0d-5a3f0e6c1d2e"], $block->calendarEvent?->attendees);
        $this->assertSame("10:01", $block->recording?->startTime?->format("H:i"));
        $this->assertSame("10:29", $block->recording?->endTime?->format("H:i"));
        $this->assertSame("9b1b1c3e-0f4b-4d2b-8f2e-3f2b25f30a52", $block->metadata()->createdBy?->id);

        $this->assertEquals($block, BlockFactory::fromArray($array));
        $this->assertEquals($array, $block->toArray());
    }

    public function test_create_from_minimal_array(): void
    {
        $array = $this->mockArray();
        $array["meeting_notes"] = [ "is_full_page" => false ];

        $block = MeetingNotes::fromArray($array);

        $this->assertFalse($block->isFullPage);
        $this->assertSame([], $block->title);
        $this->assertNull($block->status);
        $this->assertNull($block->tabs);
        $this->assertNull($block->calendarEvent);
        $this->assertNull($block->recording);
        $this->assertSame(
            [ "is_full_page" => false, "title" => [] ],
            $block->toArray()["meeting_notes"],
        );
    }

    public function test_unknown_status_is_null(): void
    {
        $array = $this->mockArray("some_future_status");

        $block = MeetingNotes::fromArray($array);

        $this->assertNull($block->status);
    }

    public function test_error_on_wrong_type(): void
    {
        $array = $this->mockArray();
        $array["type"] = "wrong-type";

        $this->expectException(BlockException::class);
        MeetingNotes::fromArray($array);
    }

    public function test_not_allow_to_change_children(): void
    {
        $block = MeetingNotes::fromArray($this->mockArray());

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren(Paragraph::fromString("Sample paragraph."));
    }

    public function test_not_allow_to_add_child(): void
    {
        $block = MeetingNotes::fromArray($this->mockArray());

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::fromString("Sample paragraph."));
    }

    public function test_delete(): void
    {
        $block = MeetingNotes::fromArray($this->mockArray())->delete();

        $this->assertTrue($block->metadata()->inTrash);
    }

    /** @return array{ type: string, ... } */
    private function mockArray(string $status = "notes_ready"): array
    {
        return [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2026-01-15T10:00:00.000000Z",
            "last_edited_time" => "2026-01-15T10:30:00.000000Z",
            "created_by"       => [
                "object" => "user",
                "id"     => "9b1b1c3e-0f4b-4d2b-8f2e-3f2b25f30a52",
            ],
            "last_edited_by"   => [
                "object" => "user",
                "id"     => "9b1b1c3e-0f4b-4d2b-8f2e-3f2b25f30a52",
            ],
            "in_trash"         => false,
            "has_children"     => true,
            "type"             => "meeting_notes",
            "meeting_notes"    => [
                "is_full_page"   => true,
                "title"          => [[
                    "plain_text"  => "Weekly sync",
                    "href"        => null,
                    "annotations" => [
                        "bold"          => false,
                        "italic"        => false,
                        "strikethrough" => false,
                        "underline"     => false,
                        "code"          => false,
                        "color"         => "default",
                    ],
                    "type"        => "text",
                    "text"        => [
                        "content" => "Weekly sync",
                    ],
                ]],
                "status"         => $status,
                "children"       => [
                    "summary_block_id"    => "1d5a4e3c-0f4b-4d2b-8f2e-000000000001",
                    "notes_block_id"      => "1d5a4e3c-0f4b-4d2b-8f2e-000000000002",
                    "transcript_block_id" => "1d5a4e3c-0f4b-4d2b-8f2e-000000000003",
                ],
                "calendar_event" => [
                    "start_time" => "2026-01-15T10:00:00.000000Z",
                    "end_time"   => "2026-01-15T10:30:00.000000Z",
                    "attendees"  => [ "3f2b25f3-0a52-4a90-8f0d-5a3f0e6c1d2e" ],
                    "event_id"   => "event-123",
                ],
                "recording"      => [
                    "start_time" => "2026-01-15T10:01:00.000000Z",
                    "end_time"   => "2026-01-15T10:29:00.000000Z",
                ],
            ],
        ];
    }
}
