<?php

namespace Notion\Views\Configuration;

enum SubtaskFilterScope: string
{
    case Parents            = "parents";
    case ParentsAndSubitems = "parents_and_subitems";
    case Subitems           = "subitems";
}
