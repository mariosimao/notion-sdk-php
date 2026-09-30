<?php

namespace Notion\Pages;

enum PageTemplateType: string
{
    case None = "none";
    case Default = "default";
    case TemplateId = "template_id";
}
