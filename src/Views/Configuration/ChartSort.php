<?php

namespace Notion\Views\Configuration;

enum ChartSort: string
{
    case Manual      = "manual";
    case XAscending  = "x_ascending";
    case XDescending = "x_descending";
    case YAscending  = "y_ascending";
    case YDescending = "y_descending";
}
