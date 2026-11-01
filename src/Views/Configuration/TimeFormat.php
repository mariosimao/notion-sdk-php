<?php

namespace Notion\Views\Configuration;

enum TimeFormat: string
{
    case TwelveHour     = "12_hour";
    case TwentyFourHour = "24_hour";
    case Hidden         = "hidden";
}
