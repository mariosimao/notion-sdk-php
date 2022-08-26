<?php

namespace Notion\Common;

enum MentionType: string
{
    case Page = "page";
    case Database = "database";
    case User = "user";
    case Date = "date";
}
