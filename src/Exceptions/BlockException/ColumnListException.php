<?php

namespace Notion\Exceptions\BlockException;

use Notion\Exceptions\BlockException;

final class ColumnListException extends BlockException
{
    public static function childNotColumn(): self
    {
        return new self("Column lists accept only columns as children.");
    }

    public static function widthRatiosExceedOne(float $sum): self
    {
        return new self("Column width ratios must not add up to more than 1, {$sum} given.");
    }

    public static function widthRatiosNotAddingUpToOne(float $sum): self
    {
        return new self(
            "When all columns of a column list have width ratios, they must add up to 1, {$sum} given."
        );
    }
}
