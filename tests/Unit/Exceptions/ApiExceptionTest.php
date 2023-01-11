<?php

namespace Notion\Test\Unit\Exceptions;

use GuzzleHttp\Psr7\Response;
use Notion\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ApiExceptionTest extends TestCase
{
    public function test_from_response_body(): void
    {
        $body = '{
            "object": "error",
            "status": 404,
            "code": "object_not_found",
            "message": "Could not find page with ID: f077308f-dd9e-4cfe-87e6-7420a8488514."
        }';
        /** @var array{ message: string, code: string } $bodyArray */
        $bodyArray = json_decode($body, true);
        $response = new Response(404, [], $body);

        $e = ApiException::fromResponse($response);

        $this->assertSame($bodyArray["message"], $e->getMessage());
        /** @psalm-suppress DeprecatedMethod */
        $this->assertSame($bodyArray["code"], $e->getNotionCode());
        $this->assertSame($bodyArray["code"], $e->notionCode);
        $this->assertInstanceOf(ResponseInterface::class, $e->response);
    }
}
