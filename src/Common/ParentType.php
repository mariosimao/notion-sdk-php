<?php

namespace Notion\Common;

enum ParentType: string
{
    case Database = "database_id";
    case Page = "page_id";
    case Block = "block_id";
    case Workspace = "workspace";
}
