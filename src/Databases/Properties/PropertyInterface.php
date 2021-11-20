<?php

namespace Notion\Databases\Properties;

/** @psalm-immutable */
interface PropertyInterface
{
    public static function fromArray(array $array): self;
    public function toArray(): array;

    public function property(): Property;
}
