<?php

namespace Notion\Common;

/**
 * Markdown syntax used when sending markdown content to Notion.
 *
 * `V2` is Notion-flavored Markdown and is recommended. `V1` (enhanced
 * markdown) is deprecated by Notion.
 */
enum MarkdownVersion: string
{
    case V1 = "v1";
    case V2 = "v2";
}
