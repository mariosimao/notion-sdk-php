<?php

namespace Notion\Sessions;

enum TimestampOperator: string
{
    /** Only supported when filtering session events. */
    case Equals = "equals";
    case Before = "before";
    case After = "after";
    case OnOrBefore = "on_or_before";
    case OnOrAfter = "on_or_after";
}
