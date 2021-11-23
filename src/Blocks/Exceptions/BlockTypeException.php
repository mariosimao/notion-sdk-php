<?php

namespace Notion\Blocks\Exceptions;

class BlockTypeException extends \Exception
{
    public function __construct(string $blockType)
    {
        parent::__construct("Block must be of type " . $blockType);
    }
}