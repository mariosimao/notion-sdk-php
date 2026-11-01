<?php

namespace Notion\Sessions;

enum SessionStatus: string
{
    case Queued = "queued";
    case InProgress = "in_progress";
    case RequiresAction = "requires_action";
    case Completed = "completed";
    case Failed = "failed";
    case Canceled = "canceled";
    case Terminated = "terminated";

    /** Whether the session turn is over and no further output is expected. */
    public function isTerminal(): bool
    {
        return match ($this) {
            self::Completed, self::Failed, self::Canceled, self::Terminated => true,
            default => false,
        };
    }
}
