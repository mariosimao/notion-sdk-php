<?php

namespace Notion\Exceptions;

class ColumnException extends BlockException
{
    public static function columnInsideColumn(): self
    {
        return new self("Columns should not contain other columns.");
    }
}
