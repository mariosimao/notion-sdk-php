<?php

namespace Notion\Views\Configuration;

enum TimelineZoomLevel: string
{
    case Hours     = "hours";
    case Day       = "day";
    case Week      = "week";
    case BiWeek    = "bi_week";
    case Month     = "month";
    case Quarter   = "quarter";
    case Year      = "year";
    case FiveYears = "5_years";
}
