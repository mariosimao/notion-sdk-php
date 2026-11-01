<?php

namespace Notion\Views\Configuration;

enum ChartAggregator: string
{
    case Count            = "count";
    case CountValues      = "count_values";
    case Sum              = "sum";
    case Average          = "average";
    case Median           = "median";
    case Min              = "min";
    case Max              = "max";
    case Range            = "range";
    case Unique           = "unique";
    case Empty            = "empty";
    case NotEmpty         = "not_empty";
    case PercentEmpty     = "percent_empty";
    case PercentNotEmpty  = "percent_not_empty";
    case Checked          = "checked";
    case Unchecked        = "unchecked";
    case PercentChecked   = "percent_checked";
    case PercentUnchecked = "percent_unchecked";
    case EarliestDate     = "earliest_date";
    case LatestDate       = "latest_date";
    case DateRange        = "date_range";
}
