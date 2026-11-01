<?php

namespace Notion\Agents\Query;

enum SortProperty: string
{
    case CreatedTime = "created_time";
    case LastRunAt = "last_run_at";
}
