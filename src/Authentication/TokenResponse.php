<?php

namespace Notion\Authentication;

/**
 * @psalm-import-type OwnerJson from Owner
 *
 * @psalm-type TokenResponseJson = array{
 *     access_token: string,
 *     token_type?: string,
 *     bot_id: string,
 *     workspace_id: string,
 *     workspace_name?: string|null,
 *     workspace_icon?: string|null,
 *     owner: OwnerJson,
 *     duplicated_template_id?: string|null,
 *     refresh_token?: string|null,
 *     request_id?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class TokenResponse
{
    private function __construct(
        public string $accessToken,
        public string $tokenType,
        public string $botId,
        public string $workspaceId,
        public string|null $workspaceName,
        public string|null $workspaceIcon,
        public Owner $owner,
        public string|null $duplicatedTemplateId = null,
        public string|null $refreshToken = null,
        public string|null $requestId = null,
    ) {
    }

    public static function create(
        string $accessToken,
        string $botId,
        string $workspaceId,
        Owner $owner,
        string $tokenType = "bearer",
        string|null $workspaceName = null,
        string|null $workspaceIcon = null,
        string|null $duplicatedTemplateId = null,
        string|null $refreshToken = null,
        string|null $requestId = null,
    ): self {
        return new self(
            $accessToken,
            $tokenType,
            $botId,
            $workspaceId,
            $workspaceName,
            $workspaceIcon,
            $owner,
            $duplicatedTemplateId,
            $refreshToken,
            $requestId,
        );
    }

    /**
     * @psalm-param TokenResponseJson $array
     */
    public static function fromArray(array $array): self
    {
        return new self(
            accessToken: $array["access_token"],
            tokenType: $array["token_type"] ?? "bearer",
            botId: $array["bot_id"],
            workspaceId: $array["workspace_id"],
            workspaceName: $array["workspace_name"] ?? null,
            workspaceIcon: $array["workspace_icon"] ?? null,
            owner: Owner::fromArray($array["owner"]),
            duplicatedTemplateId: $array["duplicated_template_id"] ?? null,
            refreshToken: $array["refresh_token"] ?? null,
            requestId: $array["request_id"] ?? null,
        );
    }
}
