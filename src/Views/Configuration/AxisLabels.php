<?php

namespace Notion\Views\Configuration;

enum AxisLabels: string
{
    case None  = "none";
    case XAxis = "x_axis";
    case YAxis = "y_axis";
    case Both  = "both";
}
