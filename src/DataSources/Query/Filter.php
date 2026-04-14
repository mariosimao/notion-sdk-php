<?php

namespace Notion\DataSources\Query;

/** @psalm-immutable */
interface Filter
{
    public function toArray(): array;
}
