<?php

namespace Notion\Databases\Properties;

enum RelationType: string
{
    case SingleProperty = "single_property";
    case DualProperty = "dual_property";
}
