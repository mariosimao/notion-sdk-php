<?php

namespace Notion\Test\Unit\Authentication;

use Notion\Authentication\Owner;
use Notion\Authentication\TokenResponse;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class TokenResponseTest extends TestCase
{
    public function test_from_array_with_user_owner(): void
    {
        $array = [
            "access_token" => "secret_token_123",
            "token_type" => "bearer",
            "bot_id" => "936998cb-aa55-46ff-b633-875c74239845",
            "workspace_id" => "787c805a-e7c6-48a6-9be8-1647895188f6",
            "workspace_name" => "Acme Workspace",
            "workspace_icon" => "https://example.com/icon.png",
            "owner" => [
                "type" => "user",
                "user" => [
                    "object" => "user",
                    "id" => "123e4567-e89b-12d3-a456-426614174000",
                    "name" => "Jane Doe",
                ],
            ],
            "duplicated_template_id" => "550e8400-e29b-41d4-a716-446655440000",
            "refresh_token" => "refresh_token_abc",
            "request_id" => "req_789",
        ];

        $tokenResponse = TokenResponse::fromArray($array);

        $this->assertSame("secret_token_123", $tokenResponse->accessToken);
        $this->assertSame("bearer", $tokenResponse->tokenType);
        $this->assertSame("936998cb-aa55-46ff-b633-875c74239845", $tokenResponse->botId);
        $this->assertSame("787c805a-e7c6-48a6-9be8-1647895188f6", $tokenResponse->workspaceId);
        $this->assertSame("Acme Workspace", $tokenResponse->workspaceName);
        $this->assertSame("https://example.com/icon.png", $tokenResponse->workspaceIcon);
        $this->assertTrue($tokenResponse->owner->isUser());
        $this->assertSame("Jane Doe", $tokenResponse->owner->user?->name);
        $this->assertSame("550e8400-e29b-41d4-a716-446655440000", $tokenResponse->duplicatedTemplateId);
        $this->assertSame("refresh_token_abc", $tokenResponse->refreshToken);
        $this->assertSame("req_789", $tokenResponse->requestId);
    }

    public function test_from_array_with_workspace_owner(): void
    {
        $array = [
            "access_token" => "secret_token_456",
            "bot_id" => "bot_456",
            "workspace_id" => "workspace_789",
            "owner" => [
                "type" => "workspace",
                "workspace" => true,
            ],
        ];

        $tokenResponse = TokenResponse::fromArray($array);

        $this->assertSame("secret_token_456", $tokenResponse->accessToken);
        $this->assertSame("bearer", $tokenResponse->tokenType);
        $this->assertTrue($tokenResponse->owner->isWorkspace());
        $this->assertNull($tokenResponse->workspaceName);
        $this->assertNull($tokenResponse->workspaceIcon);
        $this->assertNull($tokenResponse->duplicatedTemplateId);
        $this->assertNull($tokenResponse->refreshToken);
        $this->assertNull($tokenResponse->requestId);
    }

    public function test_multiple_installations_in_same_workspace(): void
    {
        $user1 = User::create("user_1");
        $user2 = User::create("user_2");
        $workspaceId = "shared-workspace-id";

        $installation1 = TokenResponse::fromArray([
            "access_token" => "token_user_1",
            "bot_id" => "bot_user_1",
            "workspace_id" => $workspaceId,
            "owner" => [
                "type" => "user",
                "user" => $user1->toArray(),
            ],
        ]);

        $installation2 = TokenResponse::fromArray([
            "access_token" => "token_user_2",
            "bot_id" => "bot_user_2",
            "workspace_id" => $workspaceId,
            "owner" => [
                "type" => "user",
                "user" => $user2->toArray(),
            ],
        ]);

        $this->assertSame($installation1->workspaceId, $installation2->workspaceId);
        $this->assertNotSame($installation1->botId, $installation2->botId);
        $this->assertNotSame($installation1->accessToken, $installation2->accessToken);
        $this->assertNotSame($installation1->owner->user?->id, $installation2->owner->user?->id);
    }
}
