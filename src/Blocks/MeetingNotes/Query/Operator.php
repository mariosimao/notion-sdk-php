<?php

namespace Notion\Blocks\MeetingNotes\Query;

enum Operator: string
{
    case StringIs = "string_is";
    case StringIsNot = "string_is_not";
    case StringContains = "string_contains";
    case StringDoesNotContain = "string_does_not_contain";
    case StringStartsWith = "string_starts_with";
    case StringEndsWith = "string_ends_with";
    case PersonContains = "person_contains";
    case PersonDoesNotContain = "person_does_not_contain";
    case DateIs = "date_is";
    case DateIsBefore = "date_is_before";
    case DateIsAfter = "date_is_after";
    case DateIsOnOrBefore = "date_is_on_or_before";
    case DateIsOnOrAfter = "date_is_on_or_after";
    case DateIsWithin = "date_is_within";
    case DateIsRelativeTo = "date_is_relative_to";
    case IsEmpty = "is_empty";
    case IsNotEmpty = "is_not_empty";
}
