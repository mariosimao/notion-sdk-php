<?php

namespace Notion\Sessions\Events;

enum ContentType: string
{
    case Text = "text";
    case File = "file";
}
