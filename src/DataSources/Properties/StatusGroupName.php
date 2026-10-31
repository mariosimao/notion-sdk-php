<?php

namespace Notion\DataSources\Properties;

/**
 * Default status groups that options can be assigned to.
 */
enum StatusGroupName: string
{
    case ToDo = "To-do";
    case InProgress = "In progress";
    case Complete = "Complete";
}
