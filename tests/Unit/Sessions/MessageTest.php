<?php

namespace Notion\Test\Unit\Sessions;

use Notion\Sessions\Attachment;
use Notion\Sessions\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function test_start(): void
    {
        $message = Message::start("agent-id", "Hello!");

        $this->assertSame("Hello!", $message->text);
        $this->assertSame("agent-id", $message->agentId);
        $this->assertNull($message->sessionId);
        $this->assertSame([ "message" => "Hello!", "agent_id" => "agent-id" ], $message->toArray());
    }

    public function test_start_with_notion_ai(): void
    {
        $message = Message::start(Message::NOTION_AI, "Hello!");

        $this->assertSame("notion_ai", $message->toArray()["agent_id"] ?? null);
    }

    public function test_reply(): void
    {
        $message = Message::reply("session-id", "Hello!");

        $this->assertNull($message->agentId);
        $this->assertSame([ "message" => "Hello!", "session_id" => "session-id" ], $message->toArray());
    }

    public function test_all_options(): void
    {
        $message = Message::start("agent-id", "Hello!")
            ->changeText("Summarize the file")
            ->addAttachment(Attachment::fileUpload("upload-1", "report.pdf"))
            ->addAttachment(Attachment::fileUpload("upload-2"))
            ->changeMetadata([ "source" => "slack" ])
            ->changePromptContext("The user is in the #general channel.");

        $this->assertSame([
            "message" => "Summarize the file",
            "agent_id" => "agent-id",
            "attachments" => [
                [ "type" => "file_upload", "file_upload" => [ "id" => "upload-1" ], "name" => "report.pdf" ],
                [ "type" => "file_upload", "file_upload" => [ "id" => "upload-2" ] ],
            ],
            "metadata" => [ "source" => "slack" ],
            "prompt_context" => "The user is in the #general channel.",
        ], $message->toArray());
    }

    public function test_change_attachments_and_remove_prompt_context(): void
    {
        $message = Message::reply("session-id", "Hello!")
            ->addAttachment(Attachment::fileUpload("upload-1"))
            ->changeAttachments(Attachment::fileUpload("upload-2"))
            ->changePromptContext("Context")
            ->removePromptContext();

        $this->assertCount(1, $message->attachments);
        $this->assertSame("upload-2", $message->attachments[0]->fileUploadId);
        $this->assertNull($message->promptContext);
    }
}
