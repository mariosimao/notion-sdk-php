<?php

namespace Notion\Pages\Markdown;

/** @psalm-immutable */
interface MarkdownUpdateInterface
{
    /** @return array<string, mixed> */
    public function toArray(): array;
}
