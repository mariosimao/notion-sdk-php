<?php

namespace Notion\Databases\Query;

/** @psalm-immutable */
interface Filter
{
    public function toArray(): array;
}
