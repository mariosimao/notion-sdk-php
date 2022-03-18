<?php

namespace Notion\Blocks;

/** @psalm-immutable */
interface BlockInterface
{
    public static function fromArray(array $array): self;
    public function toArray(): array;
    public function toUpdateArray(): array;

    public function block(): Block;
    /** @param list<BlockInterface> $children */
    public function changeChildren(array $children): self;
}
