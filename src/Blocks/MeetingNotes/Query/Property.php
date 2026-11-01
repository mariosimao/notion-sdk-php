<?php

namespace Notion\Blocks\MeetingNotes\Query;

/** Meeting note properties available for filtering and sorting */
enum Property: string
{
    case Title = "title";
    case Attendees = "attendees";
    case CreatedTime = "created_time";
    case CreatedBy = "created_by";
    case LastEditedTime = "last_edited_time";
    case LastEditedBy = "last_edited_by";
}
