<?php

namespace Notion\Blocks\MeetingNotes\Query;

/** @psalm-immutable */
interface PropertyFilter extends Filter
{
    public function property(): Property;
    public function operator(): Operator;
}
