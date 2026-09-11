<?php

namespace Notion\Test\Unit\Authentication;

use Notion\Authentication\TokenIntrospection;
use PHPUnit\Framework\TestCase;

class TokenIntrospectionTest extends TestCase
{
    public function test_from_array_active(): void
    {
        $array = [
            "active" => true,
            "scope" => "read:users,write:pages",
            "iat" => 1710000000,
            "request_id" => "req_introspect_123",
        ];

        $introspection = TokenIntrospection::fromArray($array);

        $this->assertTrue($introspection->active);
        $this->assertTrue($introspection->isActive());
        $this->assertSame("read:users,write:pages", $introspection->scope);
        $this->assertSame(1710000000, $introspection->iat);
        $this->assertSame("req_introspect_123", $introspection->requestId);
    }

    public function test_from_array_inactive(): void
    {
        $array = [
            "active" => false,
        ];

        $introspection = TokenIntrospection::fromArray($array);

        $this->assertFalse($introspection->active);
        $this->assertFalse($introspection->isActive());
        $this->assertNull($introspection->scope);
        $this->assertNull($introspection->iat);
        $this->assertNull($introspection->requestId);
    }

    public function test_create(): void
    {
        $introspection = TokenIntrospection::create(true, "read:content", 1720000000);

        $this->assertTrue($introspection->isActive());
        $this->assertSame("read:content", $introspection->scope);
        $this->assertSame(1720000000, $introspection->iat);
    }
}
