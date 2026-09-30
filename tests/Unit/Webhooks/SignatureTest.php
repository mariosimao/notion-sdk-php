<?php

namespace Notion\Test\Unit\Webhooks;

use Notion\Webhooks\Signature;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase
{
    // Reference value computed with `openssl dgst -sha256 -hmac <token>`
    private const TOKEN = "test-verification-token";
    private const BODY = '{"verification_token":"test-verification-token"}';
    private const SIGNATURE = "sha256=e30370dc1c829ea85cd464f92d19f35efa6d090eb9e8b995eddb19328169199b";

    public function test_sign(): void
    {
        $this->assertSame(self::SIGNATURE, Signature::sign(self::BODY, self::TOKEN));
    }

    public function test_verify_valid_signature(): void
    {
        $this->assertTrue(Signature::verify(self::BODY, self::SIGNATURE, self::TOKEN));
    }

    public function test_verify_rejects_tampered_body(): void
    {
        $body = '{"verification_token": "test-verification-token"}';

        $this->assertFalse(Signature::verify($body, self::SIGNATURE, self::TOKEN));
    }

    public function test_verify_rejects_wrong_token(): void
    {
        $this->assertFalse(Signature::verify(self::BODY, self::SIGNATURE, "other-token"));
    }

    public function test_verify_rejects_signature_without_prefix(): void
    {
        $signature = substr(self::SIGNATURE, strlen("sha256="));

        $this->assertFalse(Signature::verify(self::BODY, $signature, self::TOKEN));
    }

    public function test_signed_payload_can_be_verified(): void
    {
        $body = '{"type":"page.created"}';
        $signature = Signature::sign($body, "test-token");

        $this->assertTrue(Signature::verify($body, $signature, "test-token"));
    }
}
