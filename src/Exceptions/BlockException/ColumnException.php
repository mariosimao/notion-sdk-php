<?php

namespace Notion\Exceptions\BlockException;

use Notion\Exceptions\BlockException;

final class ColumnException extends BlockException
{
    public static function columnInsideColumn(): self
    {
        return new self("Columns should not contain other columns.");
    }
}
