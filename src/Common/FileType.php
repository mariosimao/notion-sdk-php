<?php

namespace Notion\Common;

enum FileType: string
{
    case Internal = "file";
    case External = "external";
}
