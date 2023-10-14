<?php

namespace Notion\Exceptions\BlockException;

class HeadingException extends BlockException
{
    public static function untogglifyWithChildren(): self
    {
        return new self("Heading cannot be un-togglified with children. Please remove child blocks");
    }
}
