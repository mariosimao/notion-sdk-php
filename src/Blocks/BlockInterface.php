<?php

namespace Notion\Blocks;

/** @psalm-immutable */
interface BlockInterface
{
    public static function fromArray(array $array): self;
    public function toArray(): array;

    public function block(): Block;
}
