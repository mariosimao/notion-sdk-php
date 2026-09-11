<?php

namespace Notion\Authentication;

use Notion\Configuration;
use Notion\Exceptions\ApiException;
use Notion\Infrastructure\Http;

/**
 * @psalm-import-type TokenResponseJson from TokenResponse
 * @psalm-import-type TokenIntrospectionJson from TokenIntrospection
 */
final readonly class Client
{
    /**
     * @internal Use `\Notion\Notion::authentication()` instead
     */
    public function __construct(
        private Configuration $config,
        public string $clientId,
        public string $clientSecret,
    ) {
    }

    public function createToken(
        string $code,
        string|null $redirectUri = null,
        ExternalAccount|null $externalAccount = null,
    ): TokenResponse {
        $body = [
            "grant_type" => "authorization_code",
            "code" => $code,
        ];

        if ($redirectUri !== null) {
            $body["redirect_uri"] = $redirectUri;
        }

        if ($externalAccount !== null) {
            $body["external_account"] = $externalAccount->toArray();
        }

        /** @psalm-var TokenResponseJson $responseBody */
        $responseBody = $this->sendBasicAuthRequest(
            "https://api.notion.com/v1/oauth/token",
            $body,
        );

        return TokenResponse::fromArray($responseBody);
    }

    public function refreshToken(string $refreshToken): TokenResponse
    {
        $body = [
            "grant_type" => "refresh_token",
            "refresh_token" => $refreshToken,
        ];

        /** @psalm-var TokenResponseJson $responseBody */
        $responseBody = $this->sendBasicAuthRequest(
            "https://api.notion.com/v1/oauth/token",
            $body,
        );

        return TokenResponse::fromArray($responseBody);
    }

    public function introspectToken(string $token): TokenIntrospection
    {
        $body = [
            "token" => $token,
        ];

        /** @psalm-var TokenIntrospectionJson $responseBody */
        $responseBody = $this->sendBasicAuthRequest(
            "https://api.notion.com/v1/oauth/introspect",
            $body,
        );

        return TokenIntrospection::fromArray($responseBody);
    }

    public function revokeToken(string $token): void
    {
        $body = [
            "token" => $token,
        ];

        $this->sendBasicAuthRequest(
            "https://api.notion.com/v1/oauth/revoke",
            $body,
        );
    }

    /**
     * @param array<string, mixed> $body
     */
    private function sendBasicAuthRequest(string $url, array $body): array
    {
        $encoded = base64_encode("{$this->clientId}:{$this->clientSecret}");
        $auth = "Basic {$encoded}";

        $request = $this->config->requestFactory
            ->createRequest("POST", $url)
            ->withHeader("Authorization", $auth)
            ->withHeader("Notion-Version", $this->config->version)
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write((string) json_encode($body));

        $response = $this->config->httpClient->sendRequest($request);

        /** @var array */
        $responseBody = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() >= 400) {
            /** @var mixed $rawMessage */
            $rawMessage = $responseBody["message"]
                ?? $responseBody["error_description"]
                ?? $responseBody["error"]
                ?? "";
            /** @var mixed $rawCode */
            $rawCode = $responseBody["code"] ?? $responseBody["error"] ?? "";

            $message = is_string($rawMessage) ? $rawMessage : "";
            $code = is_string($rawCode) ? $rawCode : "";

            throw new ApiException($message, $code, $response);
        }

        return $responseBody;
    }
}
