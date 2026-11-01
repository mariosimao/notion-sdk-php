<?php

namespace Notion\Exceptions\BlockException;

use Notion\Exceptions\BlockException;

final class TabException extends BlockException
{
    public static function childNotParagraph(): self
    {
        return new self("Tab blocks accept only paragraphs as children.");
    }
}
