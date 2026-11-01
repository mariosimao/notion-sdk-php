<?php

namespace Notion\Sessions;

enum StreamEventType: string
{
    /** The session's state at the start of the stream. */
    case SessionSnapshot = "session.snapshot";
    /** Output the agent is still producing. It may be superseded; don't persist it. */
    case EventProvisional = "event.provisional";
    /** A durable session event. */
    case EventCommitted = "event.committed";
    /** The stream ended early while the turn is still running. Reconnect with `resumeStream()`. */
    case StreamTimeout = "stream.timeout";
    /** The current turn is over. */
    case StreamEnd = "stream.end";
    /** The turn failed. */
    case StreamError = "stream.error";
    case Unknown = "unknown";
}
