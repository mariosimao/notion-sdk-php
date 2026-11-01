<?php

namespace Notion\Sessions;

use Notion\Sessions\Events\EventFactory;
use Notion\Sessions\Events\SessionEvent;

/**
 * A server-sent event received while streaming a session turn.
 *
 * Which properties are filled depends on `$type`:
 *  - `SessionSnapshot`: `$session`
 *  - `EventProvisional` and `EventCommitted`: `$event`
 *  - `StreamTimeout`: `$sessionId` and `$message`
 *  - `StreamEnd`: `$sessionId` and `$status`
 *  - `StreamError`: `$error` and optionally `$sessionId`
 *
 * @psalm-import-type SessionJson from Session
 * @psalm-import-type SessionEventJson from SessionEvent
 * @psalm-import-type SessionErrorJson from SessionError
 *
 * @psalm-type StreamEventJson = array{
 *      type: string,
 *      session?: SessionJson,
 *      event?: SessionEventJson,
 *      session_id?: string,
 *      message?: string,
 *      status?: string,
 *      error?: SessionErrorJson,
 * }
 *
 * @psalm-immutable
 */
final readonly class StreamEvent
{
    private function __construct(
        public StreamEventType $type,
        public Session|null $session,
        public SessionEvent|null $event,
        public string|null $sessionId,
        public string|null $message,
        public SessionStatus|null $status,
        public SessionError|null $error,
    ) {
    }

    /**
     * @param StreamEventJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $session = $array["session"] ?? null;
        $event = $array["event"] ?? null;
        $status = $array["status"] ?? null;
        $error = $array["error"] ?? null;

        return new self(
            StreamEventType::tryFrom($array["type"]) ?? StreamEventType::Unknown,
            $session !== null ? Session::fromArray($session) : null,
            $event !== null ? EventFactory::fromArray($event) : null,
            $array["session_id"] ?? $session["id"] ?? $event["session_id"] ?? null,
            $array["message"] ?? null,
            $status !== null ? SessionStatus::from($status) : null,
            $error !== null ? SessionError::fromArray($error) : null,
        );
    }

    /**
     * Whether no more events will be sent for the current turn: it ended,
     * failed, or the stream timed out.
     */
    public function isFinal(): bool
    {
        return match ($this->type) {
            StreamEventType::StreamEnd, StreamEventType::StreamError, StreamEventType::StreamTimeout => true,
            default => false,
        };
    }
}
