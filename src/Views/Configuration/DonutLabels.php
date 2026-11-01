<?php

namespace Notion\Views\Configuration;

enum DonutLabels: string
{
    case None         = "none";
    case Value        = "value";
    case Name         = "name";
    case NameAndValue = "name_and_value";
}
