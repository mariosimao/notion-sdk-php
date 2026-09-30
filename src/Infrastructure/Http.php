<?php

namespace Notion\Infrastructure;

use Notion\Configuration;
use Notion\Exceptions\ApiException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final readonly class Http
{
    public static function parseBody(ResponseInterface $response): array
    {
        /** @var array */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() >= 400) {
            throw ApiException::fromResponse($response);
        }

        return $body;
    }

    public static function createRequest(string $uri, Configuration $config): RequestInterface
    {
        return $config->requestFactory
            ->createRequest("GET", $uri)
            ->withHeader("Authorization", "Bearer {$config->token}")
            ->withHeader("Notion-Version", $config->version);
    }

    public static function createAuthRequest(
        string $uri,
        Configuration $config,
        string $clientId,
        string $clientSecret,
    ): RequestInterface {
        return $config->requestFactory
            ->createRequest("GET", $uri)
            ->withHeader("Authorization", "Basic " . base64_encode("{$clientId}:{$clientSecret}"))
            ->withHeader("Notion-Version", $config->version);
    }

    public static function sendRequest(RequestInterface $request, Configuration $config): array
    {
        $policy = $config->retryPolicy;
        $retries = 0;

        while (true) {
            $response = $config->httpClient->sendRequest($request);

            try {
                return self::parseBody($response);
            } catch (ApiException $e) {
                if (!$policy->shouldRetry($request->getMethod(), $e, $retries)) {
                    throw $e;
                }
            }

            $delayMs = $policy->delayMs($response, $retries);
            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }

            $body = $request->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }

            $retries++;
        }
    }
}
