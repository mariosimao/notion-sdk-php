<?php

namespace Notion\Pages;

enum PageParentType: string
{
    case Page = "page_id";
    case Database = "database_id";
    case Workspace = "workspace";
    case Block = "block_id";
}
