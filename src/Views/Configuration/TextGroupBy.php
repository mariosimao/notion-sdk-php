<?php

namespace Notion\Views\Configuration;

enum TextGroupBy: string
{
    case Exact          = "exact";
    case AlphabetPrefix = "alphabet_prefix";
}
