<?php

namespace Notion\Agents\Batch;

enum TaskStatus: string
{
    case Queued = "queued";
    case Running = "running";
    case Retrying = "retrying";
    case Succeeded = "succeeded";
    case Failed = "failed";
}
