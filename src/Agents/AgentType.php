<?php

namespace Notion\Agents;

enum AgentType: string
{
    case NotionAi = "notion_ai";
    case CustomAgent = "custom_agent";
    case AutofillCustomAgent = "autofill_custom_agent";
    case External = "external";
}
