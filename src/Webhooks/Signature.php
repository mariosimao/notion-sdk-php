<?php

namespace Notion\Webhooks;

/** Generates and verifies `X-Notion-Signature` header values. */
final readonly class Signature
{
    public const HEADER = "X-Notion-Signature";

    private const PREFIX = "sha256=";

    /**
     * @param string $body Raw request body, exactly as received.
     * @param string $verificationToken Token received when the webhook subscription was verified.
     */
    public static function sign(string $body, string $verificationToken): string
    {
        return self::PREFIX . hash_hmac("sha256", $body, $verificationToken);
    }

    /**
     * @param string $body Raw request body, exactly as received.
     * @param string $signature Value of the `X-Notion-Signature` header.
     * @param string $verificationToken Token received when the webhook subscription was verified.
     */
    public static function verify(string $body, string $signature, string $verificationToken): bool
    {
        return hash_equals(self::sign($body, $verificationToken), $signature);
    }
}
