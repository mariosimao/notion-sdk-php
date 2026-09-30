<?php

namespace Notion\Webhooks;

enum EventParentType: string
{
    case Space = "space";
    case Team = "team";
    case Page = "page";
    case Database = "database";
    case Block = "block";
    case Agent = "agent";
}
