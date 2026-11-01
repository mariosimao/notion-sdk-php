<?php

namespace Notion\Views\Configuration;

enum GroupSort: string
{
    case Manual     = "manual";
    case Ascending  = "ascending";
    case Descending = "descending";
}
