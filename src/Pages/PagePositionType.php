<?php

namespace Notion\Pages;

enum PagePositionType: string
{
    case AfterBlock = "after_block";
    case PageStart = "page_start";
    case PageEnd = "page_end";
}
