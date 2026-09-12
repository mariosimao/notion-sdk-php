<?php

namespace Notion\Pages\PropertyItems;

enum RollupType: string
{
    case Array = "array";
    case Date = "date";
    case Incomplete = "incomplete";
    case Number = "number";
    case Unsupported = "unsupported";
}
