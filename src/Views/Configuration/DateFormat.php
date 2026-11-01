<?php

namespace Notion\Views\Configuration;

enum DateFormat: string
{
    case Full         = "full";
    case Short        = "short";
    case MonthDayYear = "month_day_year";
    case DayMonthYear = "day_month_year";
    case YearMonthDay = "year_month_day";
    case Relative     = "relative";
}
