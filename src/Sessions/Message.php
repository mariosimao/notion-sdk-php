<?php

namespace Notion\Sessions;

/**
 * A message to start a session or to continue an existing one.
 *
 * @psalm-import-type AttachmentJson from Attachment
 *
 * @psalm-type MessageJson = array{
 *      message: string,
 *      agent_id?: string,
 *      session_id?: string,
 *      attachments?: list<AttachmentJson>,
 *      metadata?: array<string, string>,
 *      prompt_context?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Message
{
    /** Agent ID for the user's personal Notion AI agent. */
    public const NOTION_AI = "notion_ai";

    /**
     * @param list<Attachment> $attachments
     * @param array<string, string> $metadata
     */
    private function __construct(
        public string $text,
        public string|null $agentId,
        public string|null $sessionId,
        public array $attachments,
        public array $metadata,
        public string|null $promptContext,
    ) {
    }

    /**
     * Start a new session with a Custom Agent.
     *
     * Use `Message::NOTION_AI` as agent ID to chat with your personal agent.
     */
    public static function start(string $agentId, string $text): self
    {
        return new self($text, $agentId, null, [], [], null);
    }

    /** Send a message to an existing session. */
    public static function reply(string $sessionId, string $text): self
    {
        return new self($text, null, $sessionId, [], [], null);
    }

    public function changeText(string $text): self
    {
        return new self(
            $text,
            $this->agentId,
            $this->sessionId,
            $this->attachments,
            $this->metadata,
            $this->promptContext,
        );
    }

    public function addAttachment(Attachment $attachment): self
    {
        return $this->changeAttachments(...[...$this->attachments, $attachment]);
    }

    public function changeAttachments(Attachment ...$attachments): self
    {
        return new self(
            $this->text,
            $this->agentId,
            $this->sessionId,
            array_values($attachments),
            $this->metadata,
            $this->promptContext,
        );
    }

    /**
     * Key-value pairs stored with the message (up to 20 entries).
     *
     * @param array<string, string> $metadata
     */
    public function changeMetadata(array $metadata): self
    {
        return new self(
            $this->text,
            $this->agentId,
            $this->sessionId,
            $this->attachments,
            $metadata,
            $this->promptContext,
        );
    }

    /** Extra context given to the agent for this message. */
    public function changePromptContext(string $promptContext): self
    {
        return new self(
            $this->text,
            $this->agentId,
            $this->sessionId,
            $this->attachments,
            $this->metadata,
            $promptContext,
        );
    }

    public function removePromptContext(): self
    {
        return new self(
            $this->text,
            $this->agentId,
            $this->sessionId,
            $this->attachments,
            $this->metadata,
            null,
        );
    }

    /**
     * @internal
     *
     * @return MessageJson
     */
    public function toArray(): array
    {
        $array = [ "message" => $this->text ];

        if ($this->agentId !== null) {
            $array["agent_id"] = $this->agentId;
        }
        if ($this->sessionId !== null) {
            $array["session_id"] = $this->sessionId;
        }
        if ($this->attachments !== []) {
            $array["attachments"] = array_map(fn (Attachment $a) => $a->toArray(), $this->attachments);
        }
        if ($this->metadata !== []) {
            $array["metadata"] = $this->metadata;
        }
        if ($this->promptContext !== null) {
            $array["prompt_context"] = $this->promptContext;
        }

        return $array;
    }
}
