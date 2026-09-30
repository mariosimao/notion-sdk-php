<?php

namespace Notion\Webhooks;

enum AuthorType: string
{
    case Person = "person";
    case Bot = "bot";
    case Agent = "agent";
}
