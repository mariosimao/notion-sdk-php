<?php

namespace Notion\Views\Configuration;

enum ChartType: string
{
    case Column = "column";
    case Bar    = "bar";
    case Line   = "line";
    case Donut  = "donut";
    case Number = "number";
}
