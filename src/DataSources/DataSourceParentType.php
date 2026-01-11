<?php

namespace Notion\DataSources;

enum DataSourceParentType: string
{
    case Database = "database_id";
    case DataSource = "data_source_id";
}
