<?php

namespace Notion\Authentication;

use Notion\Configuration;
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

        $url = "https://api.notion.com/v1/oauth/token";
        $request = Http::createAuthRequest($url, $this->config, $this->clientId, $this->clientSecret)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write((string) json_encode($body));

        /** @psalm-var TokenResponseJson $responseBody */
        $responseBody = Http::sendRequest($request, $this->config);

        return TokenResponse::fromArray($responseBody);
    }

    public function refreshToken(string $refreshToken): TokenResponse
    {
        $body = [
            "grant_type" => "refresh_token",
            "refresh_token" => $refreshToken,
        ];

        $url = "https://api.notion.com/v1/oauth/token";
        $request = Http::createAuthRequest($url, $this->config, $this->clientId, $this->clientSecret)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write((string) json_encode($body));

        /** @psalm-var TokenResponseJson $responseBody */
        $responseBody = Http::sendRequest($request, $this->config);

        return TokenResponse::fromArray($responseBody);
    }

    public function introspectToken(string $token): TokenIntrospection
    {
        $body = [
            "token" => $token,
        ];

        $url = "https://api.notion.com/v1/oauth/introspect";
        $request = Http::createAuthRequest($url, $this->config, $this->clientId, $this->clientSecret)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write((string) json_encode($body));

        /** @psalm-var TokenIntrospectionJson $responseBody */
        $responseBody = Http::sendRequest($request, $this->config);

        return TokenIntrospection::fromArray($responseBody);
    }

    public function revokeToken(string $token): void
    {
        $body = [
            "token" => $token,
        ];

        $url = "https://api.notion.com/v1/oauth/revoke";
        $request = Http::createAuthRequest($url, $this->config, $this->clientId, $this->clientSecret)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");
        $request->getBody()->write((string) json_encode($body));

        Http::sendRequest($request, $this->config);
    }
}
