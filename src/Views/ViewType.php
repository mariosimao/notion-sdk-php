<?php

namespace Notion\Views;

enum ViewType: string
{
    case Table     = "table";
    case Board     = "board";
    case List      = "list";
    case Calendar  = "calendar";
    case Timeline  = "timeline";
    case Gallery   = "gallery";
    case Form      = "form";
    case Chart     = "chart";
    case Map       = "map";
    case Dashboard = "dashboard";
}
