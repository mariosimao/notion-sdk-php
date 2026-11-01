<?php

namespace Notion\Blocks\MeetingNotes\Query;

/** @psalm-immutable */
interface Filter
{
    public function toArray(): array;
}
