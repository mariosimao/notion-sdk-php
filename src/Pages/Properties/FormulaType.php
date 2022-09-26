<?php

namespace Notion\Pages\Properties;

enum FormulaType: string
{
    case String = "string";
    case Number = "number";
    case Boolean = "boolean";
    case Date = "date";
}
