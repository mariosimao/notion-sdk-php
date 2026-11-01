<?php

namespace Notion\Exceptions\BlockException;

use Notion\Exceptions\BlockException;

final class ColumnException extends BlockException
{
    public static function columnInsideColumn(): self
    {
        return new self("Columns should not contain other columns.");
    }

    public static function invalidWidthRatio(float $widthRatio): self
    {
        return new self(
            "Column width ratio must be greater than 0 and less than or equal to 1, {$widthRatio} given."
        );
    }
}
