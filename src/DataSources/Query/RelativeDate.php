<?php

namespace Notion\DataSources\Query;

enum RelativeDate: string
{
    case Today = "today";
    case Tomorrow = "tomorrow";
    case Yesterday = "yesterday";
    case OneWeekAgo = "one_week_ago";
    case OneWeekFromNow = "one_week_from_now";
    case OneMonthAgo = "one_month_ago";
    case OneMonthFromNow = "one_month_from_now";
}
