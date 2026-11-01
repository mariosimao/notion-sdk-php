<?php

namespace Notion\Common;

enum IncompleteReason: string
{
    case QueryResultLimitReached = "query_result_limit_reached";
}
