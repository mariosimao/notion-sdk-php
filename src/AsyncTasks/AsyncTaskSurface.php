<?php

namespace Notion\AsyncTasks;

enum AsyncTaskSurface: string
{
    case Rest = "rest";
    case Mcp = "mcp";
}
