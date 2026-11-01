<?php

namespace Notion\Blocks\MeetingNotes\Query;

enum RelativeDateRange: string
{
    case ThisWeek = "this_week";
    case PastWeek = "the_past_week";
    case PastMonth = "the_past_month";
    case PastYear = "the_past_year";
    case NextWeek = "the_next_week";
    case NextMonth = "the_next_month";
    case NextYear = "the_next_year";
}
