<?php

namespace Notion\Blocks;

enum BlockParentType: string
{
    case Page = "page_id";
    case DataSource = "data_source_id";
    case Database = "database_id";
    case Block = "block_id";
    case Agent = "agent_id";
}
