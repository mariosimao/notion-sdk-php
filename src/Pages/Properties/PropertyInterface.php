<?php

namespace Notion\Pages\Properties;

/** @psalm-immutable */
interface PropertyInterface
{
    /** @internal */
    public static function fromArray(array $array): self;
    /** @internal */
    public function toArray(): array;

    public function metadata(): PropertyMetadata;
}
