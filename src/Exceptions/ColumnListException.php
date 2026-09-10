<?php

namespace Notion\Exceptions;

final class ColumnListException extends BlockException
{
    public static function childNotColumn(): self
    {
        return new self("Column lists accept only columns as children.");
    }
}
