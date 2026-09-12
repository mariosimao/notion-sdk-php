<?php

namespace Notion\Common;

enum MentionType: string
{
    case Page = "page";
    case Database = "database";
    case User = "user";
    case Date = "date";
    case LinkPreview = "link_preview";
    case TemplateMention = "template_mention";
}
