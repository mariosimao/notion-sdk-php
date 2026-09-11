<?php

namespace Notion\Authentication;

enum OwnerType: string
{
    case User = "user";
    case Workspace = "workspace";
}
