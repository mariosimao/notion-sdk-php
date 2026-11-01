<?php

namespace Notion\Views\Configuration;

enum DateGroupBy: string
{
    case Relative = "relative";
    case Day      = "day";
    case Week     = "week";
    case Month    = "month";
    case Year     = "year";
}
