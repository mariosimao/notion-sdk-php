<?php

namespace Notion\Exceptions;

final class RetryPolicyException extends NotionException
{
    public static function negativeMaxRetries(): self
    {
        return new self("Maximum number of retries cannot be negative.");
    }

    public static function negativeInitialDelay(): self
    {
        return new self("Initial retry delay cannot be negative.");
    }

    public static function maxDelayLowerThanInitialDelay(): self
    {
        return new self("Maximum retry delay cannot be lower than the initial retry delay.");
    }
}
