<?php

namespace Notion\Exceptions;

final class ColumnException extends BlockException
{
    public static function columnInsideColumn(): self
    {
        return new self("Columns should not contain other columns.");
    }
}
