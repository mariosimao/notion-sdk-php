<?php

namespace Notion\Infrastructure;

use Notion\Configuration;
use Notion\Exceptions\ApiException;
use Notion\Exceptions\ConflictException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Http
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

    public static function sendRequest(
        RequestInterface $request,
        Configuration $config,
        int $currentAttempt = 0,
    ): array {
        $response = $config->httpClient->sendRequest($request);

        try {
            $body = self::parseBody($response);
        } catch (ConflictException $e) {
            if (
                !$config->retryOnConflict ||
                $currentAttempt >= $config->retryOnConflictAttempts
            ) {
                throw $e;
            }

            // Try again
            return self::sendRequest($request, $config, $currentAttempt + 1);
        }

        return $body;
    }
}
