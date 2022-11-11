<?php

namespace Notion\Blocks;

/** @psalm-immutable */
interface BlockInterface
{
    public function metadata(): BlockMetadata;
    public function addChild(BlockInterface $child): self;
    public function changeChildren(BlockInterface ...$children): self;
    public function archive(): self;

    /** @internal */
    public static function fromArray(array $array): self;
    /** @internal */
    public function toArray(): array;
}
