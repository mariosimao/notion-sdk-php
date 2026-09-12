<?php

namespace Notion\DataSources\Properties;

enum RollupFunction: string
{
    case Average = "average";
    case Checked = "checked";
    case Count = "count";
    case CountPerGroup = "count_per_group";
    case CountValues = "count_values";
    case DateRange = "date_range";
    case EarliestDate = "earliest_date";
    case Empty = "empty";
    case LatestDate = "latest_date";
    case Max = "max";
    case Median = "median";
    case Min = "min";
    case NotEmpty = "not_empty";
    case PercentChecked = "percent_checked";
    case PercentEmpty = "percent_empty";
    case PercentNotEmpty = "percent_not_empty";
    case PercentPerGroup = "percent_per_group";
    case PercentUnchecked = "percent_unchecked";
    case Range = "range";
    case ShowOriginal = "show_original";
    case ShowUnique = "show_unique";
    case Sum = "sum";
    case Unchecked = "unchecked";
    case Unique = "unique";
}
