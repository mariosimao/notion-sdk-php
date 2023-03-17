<?php

namespace Notion\Databases\Query;

enum Operator: string
{
    case After = "after";
    case Before = "before";
    case Contains = "contains";
    case DoesNotContain = "does_not_contain";
    case DoesNotEqual = "does_not_equal";
    case EndsWith = "ends_with";
    case Equals = "equals";
    case GreaterThan = "greater_than";
    case GreaterThanOrEqualTo = "greater_than_or_equal_to";
    case IsEmpty = "is_empty";
    case IsNotEmpty = "is_not_empty";
    case LessThan = "less_than";
    case LessThanOrEqualTo = "less_than_or_equal_to";
    case NextMonth = "next_month";
    case NextWeek = "next_week";
    case NextYear = "next_year";
    case OnOrAfter = "on_or_after";
    case OnOrBefore = "on_or_before";
    case PastMonth = "past_month";
    case PastWeek = "past_week";
    case PastYear = "past_year";
    case StartsWith = "starts_with";
    case ThisWeek = "this_week";
}
