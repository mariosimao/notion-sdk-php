<?php

namespace Notion\Databases\Query;

interface Condition
{
    public function propertyType(): string;
    public function propertyName(): string;
    public function operator(): Operator;
    public function value(): mixed;
}
