<?php

namespace Notion\Pages\Properties;

interface PropertyInterface
{
    public static function fromArray(array $array): self;
    public function toArray(): array;

    public function property(): Property;
}
