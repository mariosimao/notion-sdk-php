<?php

namespace Notion\Sessions;

enum SortProperty: string
{
    /** Sessions and events */
    case CreatedAt = "created_at";
    /** Sessions only */
    case UpdatedAt = "updated_at";
    /** Events only */
    case Sequence = "sequence";
}
