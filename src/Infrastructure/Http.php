<?php

namespace Notion\Infrastructure;

use Notion\Exceptions\ApiException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Http
{
    public static function parseBody(ResponseInterface $response): array
    {
        /** @var array */
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            throw ApiException::fromResponse($response);
        }

        return $body;
    }

    public static function createRequest(
        RequestFactoryInterface $requestFactory,
        string $version,
        string $token,
        string $uri,
    ): RequestInterface {
        return $requestFactory
            ->createRequest("GET", $uri)
            ->withHeader("Authorization", "Bearer {$token}")
            ->withHeader("Notion-Version", $version);
    }
}
