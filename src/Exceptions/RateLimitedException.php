<?php

namespace Notion\Exceptions;

/**
 * The request exceeded a Notion API rate limit (HTTP 429, `rate_limited`)
 */
final class RateLimitedException extends ApiException
{
    /**
     * Which limit was exceeded (`additional_data.rate_limit_reason`), if known.
     */
    public function rateLimitReason(): string|null
    {
        return $this->additionalData->rateLimitReason;
    }

    /**
     * Whether retrying the request may succeed.
     *
     * Returns `false` when the connection's API access has been restricted
     * (`public_api_request_blocked`).
     */
    public function isRetryable(): bool
    {
        return !$this->additionalData->isRequestBlocked();
    }
}
