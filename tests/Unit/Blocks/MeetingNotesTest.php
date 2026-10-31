<?php

namespace Notion\Test\Unit\Blocks;

use DateTimeImmutable;
use Notion\Blocks\BlockFactory;
use Notion\Blocks\BlockType;
use Notion\Blocks\MeetingNotes;
use Notion\Blocks\MeetingNotesStatus;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use PHPUnit\Framework\TestCase;

class MeetingNotesTest extends TestCase
{
    /** @return array{ type: string, ... } */
    private static function fullArray(): array
    {
        return [
            "object"           => "block",
            "id"               => "d7b3c8f4-9e6e-4c1a-b5b8-2c0f4a0c5b8e",
            "created_time"     => "2026-02-24T10:00:00.000000Z",
            "last_edited_time" => "2026-02-24T10:45:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "meeting_notes",
            "meeting_notes"    => [
                "title" => [[
                    "plain_text"  => "Team Sync",
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
                        "content" => "Team Sync",
                    ],
                ]],
                "status" => "notes_ready",
                "children" => [
                    "summary_block_id"    => "a1b2c3d4-5678-9abc-def0-1234567890ab",
                    "notes_block_id"      => "b2c3d4e5-6789-abcd-ef01-234567890abc",
                    "transcript_block_id" => "c3d4e5f6-789a-bcde-f012-34567890abcd",
                ],
                "calendar_event" => [
                    "start_time" => "2026-02-24T10:00:00.000000Z",
                    "end_time"   => "2026-02-24T10:45:00.000000Z",
                    "attendees"  => [ "ee5f0f84-409a-440f-983a-a5315961c6e4" ],
                ],
                "recording" => [
                    "start_time" => "2026-02-24T10:01:00.000000Z",
                    "end_time"   => "2026-02-24T10:44:00.000000Z",
                ],
            ],
        ];
    }

    public function test_array_conversion(): void
    {
        $array = self::fullArray();

        $block = MeetingNotes::fromArray($array);

        $this->assertEquals($array, $block->toArray());
        $this->assertSame(BlockType::MeetingNotes, $block->metadata()->type);
        $this->assertSame("Team Sync", $block->toString());
        $this->assertSame(MeetingNotesStatus::NotesReady, $block->status);
        $this->assertSame("a1b2c3d4-5678-9abc-def0-1234567890ab", $block->summaryBlockId);
        $this->assertSame("b2c3d4e5-6789-abcd-ef01-234567890abc", $block->notesBlockId);
        $this->assertSame("c3d4e5f6-789a-bcde-f012-34567890abcd", $block->transcriptBlockId);
        $this->assertEquals(
            new DateTimeImmutable("2026-02-24T10:00:00.000Z"),
            $block->calendarEvent?->startTime,
        );
        $this->assertEquals(
            new DateTimeImmutable("2026-02-24T10:45:00.000Z"),
            $block->calendarEvent?->endTime,
        );
        $this->assertSame(["ee5f0f84-409a-440f-983a-a5315961c6e4"], $block->calendarEvent?->attendees);
        $this->assertEquals(
            new DateTimeImmutable("2026-02-24T10:01:00.000Z"),
            $block->recording?->startTime,
        );
        $this->assertEquals(
            new DateTimeImmutable("2026-02-24T10:44:00.000Z"),
            $block->recording?->endTime,
        );
    }

    public function test_all_fields_are_optional(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "d7b3c8f4-9e6e-4c1a-b5b8-2c0f4a0c5b8e",
            "created_time"     => "2026-02-24T10:00:00.000000Z",
            "last_edited_time" => "2026-02-24T10:45:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "meeting_notes",
            "meeting_notes"    => new \stdClass(),
        ];

        $input = $array;
        $input["meeting_notes"] = [];
        $block = MeetingNotes::fromArray($input);

        $this->assertEquals($array, $block->toArray());
        $this->assertSame([], $block->title);
        $this->assertSame("", $block->toString());
        $this->assertNull($block->status);
        $this->assertNull($block->summaryBlockId);
        $this->assertNull($block->notesBlockId);
        $this->assertNull($block->transcriptBlockId);
        $this->assertNull($block->calendarEvent);
        $this->assertNull($block->recording);
    }

    public function test_partial_nested_fields(): void
    {
        $array = self::fullArray();
        $array["meeting_notes"] = [
            "status"         => "transcription_in_progress",
            "children"       => [ "transcript_block_id" => "c3d4e5f6-789a-bcde-f012-34567890abcd" ],
            "calendar_event" => [ "start_time" => "2026-02-24T10:00:00.000000Z" ],
            "recording"      => [ "start_time" => "2026-02-24T10:01:00.000000Z" ],
        ];

        $block = MeetingNotes::fromArray($array);

        $this->assertEquals($array, $block->toArray());
        $this->assertSame(MeetingNotesStatus::TranscriptionInProgress, $block->status);
        $this->assertNull($block->summaryBlockId);
        $this->assertNull($block->notesBlockId);
        $this->assertSame([], $block->calendarEvent?->attendees);
        $this->assertNull($block->calendarEvent?->endTime);
        $this->assertNull($block->recording?->endTime);
    }

    public function test_unknown_status(): void
    {
        $array = self::fullArray();
        $array["meeting_notes"] = [ "status" => "some_future_status" ];

        $block = MeetingNotes::fromArray($array);

        $this->assertNull($block->status);
    }

    public function test_from_invalid_type(): void
    {
        $array = self::fullArray();
        $array["type"] = "paragraph";

        $this->expectException(BlockException::class);
        MeetingNotes::fromArray($array);
    }

    public function test_factory(): void
    {
        $block = BlockFactory::fromArray(self::fullArray());

        $this->assertInstanceOf(MeetingNotes::class, $block);
    }

    public function test_no_children_support(): void
    {
        $block = MeetingNotes::fromArray(self::fullArray());

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $block = MeetingNotes::fromArray(self::fullArray());

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_delete(): void
    {
        $block = MeetingNotes::fromArray(self::fullArray());

        $deleted = $block->delete();

        $this->assertInstanceOf(MeetingNotes::class, $deleted);
        $this->assertTrue($deleted->metadata()->inTrash);
        $this->assertSame("Team Sync", $deleted->toString());
        $this->assertSame(MeetingNotesStatus::NotesReady, $deleted->status);
    }
}
