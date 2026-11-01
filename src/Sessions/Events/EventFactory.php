<?php

namespace Notion\Sessions\Events;

/**
 * @psalm-import-type SessionEventJson from SessionEvent
 * @psalm-import-type UserMessageJson from UserMessage
 * @psalm-import-type AgentMessageJson from AgentMessage
 * @psalm-import-type AgentThinkingJson from AgentThinking
 * @psalm-import-type AgentToolUseJson from AgentToolUse
 * @psalm-import-type AgentToolResultJson from AgentToolResult
 * @psalm-import-type StatusChangeJson from StatusChange
 *
 * @internal
 */
final class EventFactory
{
    /** @param SessionEventJson $array */
    public static function fromArray(array $array): SessionEvent
    {
        $type = EventType::tryFrom($array["type"]) ?? EventType::Unknown;

        switch ($type) {
            case EventType::UserMessage:
                /** @psalm-var UserMessageJson $array */
                return UserMessage::fromArray($array);
            case EventType::AgentMessage:
                /** @psalm-var AgentMessageJson $array */
                return AgentMessage::fromArray($array);
            case EventType::AgentThinking:
                /** @psalm-var AgentThinkingJson $array */
                return AgentThinking::fromArray($array);
            case EventType::AgentToolUse:
                /** @psalm-var AgentToolUseJson $array */
                return AgentToolUse::fromArray($array);
            case EventType::AgentToolResult:
                /** @psalm-var AgentToolResultJson $array */
                return AgentToolResult::fromArray($array);
            case EventType::SessionStatus:
                /** @psalm-var StatusChangeJson $array */
                return StatusChange::fromArray($array);
            default:
                return UnknownEvent::fromArray($array);
        }
    }

    /**
     * @param list<array> $array
     *
     * @return list<SessionEvent>
     */
    public static function fromArrayList(array $array): array
    {
        $events = [];
        foreach ($array as $item) {
            /** @psalm-var SessionEventJson $item */
            $events[] = self::fromArray($item);
        }

        return $events;
    }
}
