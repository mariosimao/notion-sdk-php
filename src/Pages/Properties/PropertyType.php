<?php

namespace Notion\Pages\Properties;

enum PropertyType: string
{
    case Checkbox = "checkbox";
    case CreatedBy = "created_by";
    case CreatedTime = "created_time";
    case Date = "date";
    case Email = "email";
    case Files = "files";
    case Formula = "formula";
    case LastEditedBy = "last_edited_by";
    case LastEditedTime = "last_edited_time";
    case MultiSelect = "multi_select";
    case Number = "number";
    case People = "people";
    case PhoneNumber = "phone_number";
    case Relation = "relation";
    case RichText = "rich_text";
    case Rollup = "rollup";
    case Select = "select";
    case Status = "status";
    case Title = "title";
    case UniqueId = "unique_id";
    case Url = "url";
    case Unknown = "unknown";
}
