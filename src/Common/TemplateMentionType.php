<?php

namespace Notion\Common;

enum TemplateMentionType: string
{
    case Date = "template_mention_date";
    case User = "template_mention_user";
}
