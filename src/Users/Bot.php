<?php

namespace Notion\Users;

class Bot
{
    private function __construct()
    {
    }

    public static function fromArray(array $array): self
    {
        return new self();
    }

    public function toArray(): array
    {
        return [];
    }
}
