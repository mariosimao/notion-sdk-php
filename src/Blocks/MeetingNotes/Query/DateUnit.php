<?php

namespace Notion\Blocks\MeetingNotes\Query;

enum DateUnit: string
{
    case Day = "day";
    case Week = "week";
    case Month = "month";
    case Year = "year";
}
