<?php

namespace Notion\Blocks;

enum PositionType: string
{
    case Start = "start";
    case End = "end";
    case AfterBlock = "after_block";
}
