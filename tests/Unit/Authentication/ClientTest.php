<?php

namespace Notion\Test\Unit\Authentication;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Authentication\ExternalAccount;
use Notion\Configuration;
use Notion\Exceptions\ApiException;
use Notion\Notion;
use PHPUnit\Framework\TestCase;

/**
 * Proper integration tests are hard for the happy paths of the authentication client
 * due to the need for real auth codes got from browser redirect.
 * 
 * The following tests cover the API Request format and response parsing.
 */
class ClientTest extends TestCase
{
    public function test_create_token(): void
    {
        $responseBody = [
            "access_token" => "secret_oauth_token",
            "token_type" => "bearer",
            "bot_id" => "936998cb-aa55-46ff-b633-875c74239845",
            "workspace_name" => "Acme Corp",
            "workspace_icon" => "https://example.com/icon.png",
            "workspace_id" => "787c805a-e7c6-48a6-9be8-1647895188f6",
            "owner" => [
                "type" => "user",
                "user" => [
                    "object" => "user",
                    "id" => "123e4567-e89b-12d3-a456-426614174000",
                    "name" => "Jane Doe",
                ],
            ],
            "duplicated_template_id" => "550e8400-e29b-41d4-a716-446655440000",
            "refresh_token" => "refresh_token_xyz",
            "request_id" => "req_abc123",
        ];

        $mock = new MockHandler([
            new Response(200, [], (string) json_encode($responseBody)),
        ]);
        $client = $this->createNotionClient($mock);

        $externalAccount = ExternalAccount::create("ext_key", "Ext Name");
        $token = $client->authentication("my_client_id", "my_client_secret")->createToken(
            code: "auth_code_123",
            redirectUri: "https://example.com/callback",
            externalAccount: $externalAccount,
        );

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("POST", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/oauth/token", (string) $request->getUri());
        $expectedAuth = "Basic " . base64_encode("my_client_id:my_client_secret");
        $this->assertSame($expectedAuth, $request->getHeaderLine("Authorization"));
        $this->assertSame("application/json", $request->getHeaderLine("Content-Type"));

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame("authorization_code", $payload["grant_type"]);
        $this->assertSame("auth_code_123", $payload["code"]);
        $this->assertSame("https://example.com/callback", $payload["redirect_uri"]);
        $this->assertSame(["key" => "ext_key", "name" => "Ext Name"], $payload["external_account"]);

        $this->assertSame("secret_oauth_token", $token->accessToken);
        $this->assertSame("bearer", $token->tokenType);
        $this->assertSame("936998cb-aa55-46ff-b633-875c74239845", $token->botId);
        $this->assertSame("787c805a-e7c6-48a6-9be8-1647895188f6", $token->workspaceId);
        $this->assertSame("Acme Corp", $token->workspaceName);
        $this->assertTrue($token->owner->isUser());
        $this->assertSame("Jane Doe", $token->owner->user?->name);
        $this->assertSame("550e8400-e29b-41d4-a716-446655440000", $token->duplicatedTemplateId);
        $this->assertSame("refresh_token_xyz", $token->refreshToken);
        $this->assertSame("req_abc123", $token->requestId);
    }

    public function test_create_token_minimal(): void
    {
        $responseBody = [
            "access_token" => "secret_token",
            "token_type" => "bearer",
            "bot_id" => "bot_123",
            "workspace_id" => "ws_123",
            "owner" => [
                "type" => "workspace",
                "workspace" => true,
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], (string) json_encode($responseBody)),
        ]);
        $client = $this->createNotionClient($mock);

        $token = $client->authentication("client_id_val", "client_secret_val")
            ->createToken("code_only");

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $expectedAuth = "Basic " . base64_encode("client_id_val:client_secret_val");
        $this->assertSame($expectedAuth, $request->getHeaderLine("Authorization"));

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame("authorization_code", $payload["grant_type"]);
        $this->assertSame("code_only", $payload["code"]);
        $this->assertArrayNotHasKey("redirect_uri", $payload);
        $this->assertArrayNotHasKey("external_account", $payload);

        $this->assertSame("secret_token", $token->accessToken);
        $this->assertTrue($token->owner->isWorkspace());
    }

    public function test_refresh_token(): void
    {
        $responseBody = [
            "access_token" => "secret_refreshed_token",
            "token_type" => "bearer",
            "bot_id" => "bot_123",
            "workspace_id" => "ws_123",
            "owner" => [
                "type" => "workspace",
                "workspace" => true,
            ],
            "refresh_token" => "new_refresh_token",
        ];

        $mock = new MockHandler([
            new Response(200, [], (string) json_encode($responseBody)),
        ]);
        $client = $this->createNotionClient($mock);

        $token = $client->authentication("my_client_id", "my_client_secret")
            ->refreshToken("old_refresh_token");

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("POST", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/oauth/token", (string) $request->getUri());
        $expectedAuth = "Basic " . base64_encode("my_client_id:my_client_secret");
        $this->assertSame($expectedAuth, $request->getHeaderLine("Authorization"));

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame("refresh_token", $payload["grant_type"]);
        $this->assertSame("old_refresh_token", $payload["refresh_token"]);

        $this->assertSame("secret_refreshed_token", $token->accessToken);
        $this->assertSame("new_refresh_token", $token->refreshToken);
    }

    public function test_introspect_token(): void
    {
        $responseBody = [
            "active" => true,
            "scope" => "read:users",
            "iat" => 1710000000,
            "request_id" => "req_introspect",
        ];

        $mock = new MockHandler([
            new Response(200, [], (string) json_encode($responseBody)),
        ]);
        $client = $this->createNotionClient($mock);

        $introspection = $client->authentication("my_client_id", "my_client_secret")
            ->introspectToken("token_to_check");

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("POST", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/oauth/introspect", (string) $request->getUri());
        $expectedAuth = "Basic " . base64_encode("my_client_id:my_client_secret");
        $this->assertSame($expectedAuth, $request->getHeaderLine("Authorization"));

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame(["token" => "token_to_check"], $payload);

        $this->assertTrue($introspection->isActive());
        $this->assertSame("read:users", $introspection->scope);
        $this->assertSame(1710000000, $introspection->iat);
        $this->assertSame("req_introspect", $introspection->requestId);
    }

    public function test_revoke_token(): void
    {
        $mock = new MockHandler([
            new Response(200, [], "{}"),
        ]);
        $client = $this->createNotionClient($mock);

        $client->authentication("my_client_id", "my_client_secret")
            ->revokeToken("token_to_revoke");

        $request = $mock->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame("POST", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/oauth/revoke", (string) $request->getUri());
        $expectedAuth = "Basic " . base64_encode("my_client_id:my_client_secret");
        $this->assertSame($expectedAuth, $request->getHeaderLine("Authorization"));

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $request->getBody(), true);
        $this->assertSame(["token" => "token_to_revoke"], $payload);
    }

    public function test_api_exception_thrown_on_error(): void
    {
        $errorResponse = [
            "object" => "error",
            "status" => 400,
            "code" => "invalid_grant",
            "message" => "The provided authorization code is invalid.",
        ];

        $mock = new MockHandler([
            new Response(400, [], (string) json_encode($errorResponse)),
        ]);
        $client = $this->createNotionClient($mock);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage("The provided authorization code is invalid.");

        $client->authentication("client_id", "client_secret")->createToken("invalid_code");
    }

    private function createNotionClient(MockHandler $mock, string $token = "test_token"): Notion
    {
        $guzzle = new GuzzleClient(["handler" => HandlerStack::create($mock)]);
        $factory = new HttpFactory();
        $config = Configuration::createFromPsrImplementations($token, $guzzle, $factory);

        return Notion::createFromConfig($config);
    }
}
