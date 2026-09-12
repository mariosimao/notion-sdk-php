<?php

namespace Notion\Blocks;

enum LinkToPageType: string
{
    case Page = "page_id";
    case Database = "database_id";
    case Comment = "comment_id";
}
