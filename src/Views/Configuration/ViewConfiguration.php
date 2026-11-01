<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/** @psalm-immutable */
interface ViewConfiguration
{
    public function type(): ViewType;

    /** Request representation of the configuration */
    public function toArray(): array;
}
