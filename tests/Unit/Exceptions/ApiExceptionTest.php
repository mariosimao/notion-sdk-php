<?php

namespace Notion\Test\Unit\Exceptions;

use Notion\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

class ApiExceptionTest extends TestCase
{
    public function test_from_response_body(): void
    {
        /** @var array{ message: string, code: string} $responseBody */
        $responseBody = json_decode('{
            "object": "error",
            "status": 404,
            "code": "object_not_found",
            "message": "Could not find page with ID: f077308f-dd9e-4cfe-87e6-7420a8488514. Make sure the relevant pages and databases are shared with your integration."
        }', true);

        $e = ApiException::fromResponseBody($responseBody);

        $this->assertSame($responseBody["message"], $e->getMessage());
        $this->assertSame($responseBody["code"], $e->getNotionCode());
    }
}
