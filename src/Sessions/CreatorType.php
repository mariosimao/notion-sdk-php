<?php

namespace Notion\Sessions;

enum CreatorType: string
{
    case User = "user";
    case Bot = "bot";
}
