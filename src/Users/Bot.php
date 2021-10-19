<?php

namespace Notion\Users;

use Assert\Assert;

/**
 * @psalm-type BotJson = array<empty, empty>
 */
class Bot
{
    private function __construct()
    {
    }

    /** @param BotJson $array */
    public static function fromArray(array $array): self
    {
        return new self();
    }

    /** @return BotJson */
    public function toArray(): array
    {
        return [];
    }
}
