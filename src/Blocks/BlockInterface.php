<?php

namespace Notion\Blocks;

interface BlockInterface
{
    public static function fromArray(array $array): self;
    public function toArray(): array;

    public function block(): Block;
}
