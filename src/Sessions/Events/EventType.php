<?php

namespace Notion\Sessions\Events;

enum EventType: string
{
    case UserMessage = "user.message";
    case AgentMessage = "agent.message";
    case AgentThinking = "agent.thinking";
    case AgentToolUse = "agent.tool_use";
    case AgentToolResult = "agent.tool_result";
    case SessionStatus = "session.status";
    case Unknown = "unknown";
}
