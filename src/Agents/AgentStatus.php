<?php

namespace Notion\Agents;

enum AgentStatus: string
{
    case Active = "active";
    case Disabled = "disabled";
    case Deleted = "deleted";
}
