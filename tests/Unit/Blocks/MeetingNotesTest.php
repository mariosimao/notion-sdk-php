<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\BlockType;
use Notion\Blocks\MeetingNotes;
use Notion\Blocks\MeetingNotesStatus;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use PHPUnit\Framework\TestCase;

class MeetingNotesTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = $this->fullArray();

        $block = MeetingNotes::fromArray($array);

        $this->assertEquals($array, $block->toArray());
        $this->assertSame(BlockType::MeetingNotes, $block->metadata()->type);
        $this->assertSame("Team Sync", $block->toString());
        $this->assertSame(MeetingNotesStatus::NotesReady, $block->status);
        $this->assertSame("a1b2c3d4-5678-9abc-def0-1234567890ab", $block->children?->summaryBlockId);
        $this->assertSame("b2c3d4e5-6789-abcd-ef01-234567890abc", $block->children?->notesBlockId);
        $this->assertSame("c3d4e5f6-789a-bcde-f012-34567890abcd", $block->children?->transcriptBlockId);
        $this->assertSame(["ee5f0f84-409a-440f-983a-a5315961c6e4"], $block->calendarEvent?->attendees);
        $this->assertSame(
            "2026-02-24T10:00:00+00:00",
            $block->calendarEvent?->startTime->format(DATE_ATOM),
        );
        $this->assertSame(
            "2026-02-24T10:45:00+00:00",
            $block->recording?->endTime->format(DATE_ATOM),
        );
    }

    public function test_array_conversion_without_optional_fields(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "meeting_notes",
            "meeting_notes"    => [
                "title" => [],
            ],
        ];

        $block = MeetingNotes::fromArray($array);

        $this->assertEquals($array, $block->toArray());
        $this->assertSame([], $block->title);
        $this->assertNull($block->status);
        $this->assertNull($block->children);
        $this->assertNull($block->calendarEvent);
        $this->assertNull($block->recording);
    }

    public function test_missing_child_block_ids(): void
    {
        $array = $this->fullArray(overrides: [
            "children" => [
                "transcript_block_id" => "c3d4e5f6-789a-bcde-f012-34567890abcd",
            ],
        ]);

        $block = MeetingNotes::fromArray($array);

        $this->assertNull($block->children?->summaryBlockId);
        $this->assertNull($block->children?->notesBlockId);
        $this->assertEquals($array, $block->toArray());
    }

    public function test_unknown_status(): void
    {
        $array = $this->fullArray(overrides: ["status" => "some_new_status"]);

        $block = MeetingNotes::fromArray($array);

        $this->assertNull($block->status);
    }

    public function test_legacy_transcription_type(): void
    {
        $array = $this->fullArray("transcription");

        $block = BlockFactory::fromArray($array);

        $this->assertInstanceOf(MeetingNotes::class, $block);
        $this->assertSame(MeetingNotesStatus::NotesReady, $block->status);
        $this->assertEquals($this->fullArray(), $block->toArray());
    }

    public function test_from_invalid_type(): void
    {
        $array = $this->fullArray(type: "paragraph", contentKey: "meeting_notes");

        $this->expectException(BlockException::class);
        MeetingNotes::fromArray($array);
    }

    public function test_factory(): void
    {
        $block = BlockFactory::fromArray($this->fullArray());

        $this->assertInstanceOf(MeetingNotes::class, $block);
    }

    public function test_no_children_support(): void
    {
        $block = MeetingNotes::fromArray($this->fullArray());

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $block = MeetingNotes::fromArray($this->fullArray());

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_delete(): void
    {
        $block = MeetingNotes::fromArray($this->fullArray());

        $block = $block->delete();

        $this->assertTrue($block->metadata()->inTrash);
    }

    /** @return array{type: string, ...} */
    private function fullArray(
        string $type = "meeting_notes",
        array $overrides = [],
        string|null $contentKey = null,
    ): array {
        $content = [
            "title" => [
                [
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
                    "type" => "text",
                    "text" => [
                        "content" => "Team Sync",
                    ],
                ],
            ],
            "status"   => "notes_ready",
            "children" => [
                "summary_block_id"    => "a1b2c3d4-5678-9abc-def0-1234567890ab",
                "notes_block_id"      => "b2c3d4e5-6789-abcd-ef01-234567890abc",
                "transcript_block_id" => "c3d4e5f6-789a-bcde-f012-34567890abcd",
            ],
            "calendar_event" => [
                "attendees"  => ["ee5f0f84-409a-440f-983a-a5315961c6e4"],
                "start_time" => "2026-02-24T10:00:00.000000Z",
                "end_time"   => "2026-02-24T10:45:00.000000Z",
            ],
            "recording" => [
                "start_time" => "2026-02-24T10:00:00.000000Z",
                "end_time"   => "2026-02-24T10:45:00.000000Z",
            ],
        ];

        return [
            "object"                => "block",
            "id"                    => "d7b3c8f4-9e6e-4c1a-b5b8-2c0f4a0c5b8e",
            "created_time"          => "2021-10-18T17:09:00.000000Z",
            "last_edited_time"      => "2021-10-18T17:09:00.000000Z",
            "in_trash"              => false,
            "has_children"          => true,
            "type"                  => $type,
            ($contentKey ?? $type)  => array_merge($content, $overrides),
        ];
    }
}
