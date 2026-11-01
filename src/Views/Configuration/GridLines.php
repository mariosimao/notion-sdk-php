<?php

namespace Notion\Views\Configuration;

enum GridLines: string
{
    case None       = "none";
    case Horizontal = "horizontal";
    case Vertical   = "vertical";
    case Both       = "both";
}
