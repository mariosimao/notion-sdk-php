<?php

namespace Notion\Webhooks;

enum EntityType: string
{
    case Page = "page";
    case Block = "block";
    case Database = "database";
    case DataSource = "data_source";
    case Comment = "comment";
    case FileUpload = "file_upload";
    case View = "view";
}
