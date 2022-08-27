<?php

namespace Notion\Blocks\Exceptions;

class ColumnListException extends BlockException
{
    public static function childNotColumn(): self
    {
        return new self("Column lists accept only columns as children.");
    }
}
