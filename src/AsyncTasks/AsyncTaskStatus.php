<?php

namespace Notion\AsyncTasks;

enum AsyncTaskStatus: string
{
    case Queued = "queued";
    case Running = "running";
    case Retrying = "retrying";
    case Succeeded = "succeeded";
    case Failed = "failed";

    public function isTerminal(): bool
    {
        return $this === self::Succeeded || $this === self::Failed;
    }
}
