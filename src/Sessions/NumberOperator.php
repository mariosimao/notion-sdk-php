<?php

namespace Notion\Sessions;

enum NumberOperator: string
{
    case GreaterThan = "greater_than";
    case GreaterThanOrEqualTo = "greater_than_or_equal_to";
    case LessThan = "less_than";
    case LessThanOrEqualTo = "less_than_or_equal_to";
}
