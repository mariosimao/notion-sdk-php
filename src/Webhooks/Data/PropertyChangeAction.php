<?php

namespace Notion\Webhooks\Data;

enum PropertyChangeAction: string
{
    case Created = "created";
    case Updated = "updated";
    case Deleted = "deleted";
}
