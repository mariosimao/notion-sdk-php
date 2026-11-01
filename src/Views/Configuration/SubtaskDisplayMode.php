<?php

namespace Notion\Views\Configuration;

enum SubtaskDisplayMode: string
{
    case Show      = "show";
    case Hidden    = "hidden";
    case Flattened = "flattened";
    case Disabled  = "disabled";
}
