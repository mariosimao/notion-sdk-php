<?php

namespace Notion\Agents;

/**
 * Integration a Custom Agent is connected to (Notion, Slack, MCP servers, etc.)
 *
 * @psalm-import-type AgentConnectionPermissionJson from AgentConnectionPermission
 *
 * @psalm-type AgentConnectionJson = array{
 *     type: string,
 *     name: string,
 *     account?: array<string, mixed>|"hidden"|null,
 *     permissions?: list<AgentConnectionPermissionJson>,
 *     enabled_tools?: list<array{ name: string, title: string|null }>|"hidden"|null,
 *     run_tools_automatically?: array{ read: bool, write: bool },
 * }
 *
 * @psalm-immutable
 */
final readonly class AgentConnection
{
    /**
     * @param array<string, mixed>|Hidden|null $account
     * @param list<AgentConnectionPermission> $permissions
     * @param list<string>|Hidden|null $enabledTools Names of the enabled MCP tools
     */
    private function __construct(
        public string $type,
        public string $name,
        public array|Hidden|null $account,
        public array $permissions,
        public array|Hidden|null $enabledTools,
        public bool|null $runReadToolsAutomatically,
        public bool|null $runWriteToolsAutomatically,
    ) {
    }

    /**
     * @param AgentConnectionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $account = $array["account"] ?? null;
        if ($account === "hidden") {
            $account = Hidden::Hidden;
        }

        $tools = $array["enabled_tools"] ?? null;
        if ($tools === "hidden") {
            $tools = Hidden::Hidden;
        } elseif (is_array($tools)) {
            $tools = array_map(fn(array $tool): string => $tool["name"], $tools);
        }

        $permissions = array_map(
            fn(array $p): AgentConnectionPermission => AgentConnectionPermission::fromArray($p),
            $array["permissions"] ?? [],
        );

        return new self(
            $array["type"],
            $array["name"],
            $account,
            $permissions,
            $tools,
            $array["run_tools_automatically"]["read"] ?? null,
            $array["run_tools_automatically"]["write"] ?? null,
        );
    }
}
