<?php

namespace Notion\Pages;

enum PageParentType: string
{
    case Page = "page_id";
    case DataSource = "data_source_id";
    case Workspace = "workspace";
    case Block = "block_id";
}
