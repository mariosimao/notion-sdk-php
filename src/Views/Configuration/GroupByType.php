<?php

namespace Notion\Views\Configuration;

enum GroupByType: string
{
    case Select         = "select";
    case MultiSelect    = "multi_select";
    case Status         = "status";
    case Person         = "person";
    case CreatedBy      = "created_by";
    case LastEditedBy   = "last_edited_by";
    case Relation       = "relation";
    case Date           = "date";
    case CreatedTime    = "created_time";
    case LastEditedTime = "last_edited_time";
    case Text           = "text";
    case Title          = "title";
    case Url            = "url";
    case Email          = "email";
    case PhoneNumber    = "phone_number";
    case Number         = "number";
    case Checkbox       = "checkbox";
    case Formula        = "formula";
}
