<?php

namespace Notion\Databases\Query;

interface Condition
{
    /** @return "property"|"timestamp" */
    public function propertyType(): string;
    public function propertyName(): string;
    /** @return static::OPERATOR_* */
    public function operator(): string;
    public function value(): mixed;
}
