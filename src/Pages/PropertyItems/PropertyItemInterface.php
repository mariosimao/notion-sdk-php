<?php

namespace Notion\Pages\PropertyItems;

/** @psalm-immutable */
interface PropertyItemInterface
{
    /** @internal */
    public static function fromArray(array $array): self;

    /** @internal */
    public function toArray(): array;

    public function metadata(): PropertyItemMetadata;
}
