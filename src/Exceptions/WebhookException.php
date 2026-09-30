<?php

namespace Notion\Exceptions;

use Throwable;

final class WebhookException extends NotionException
{
    public static function invalidPayload(Throwable|null $previous = null): self
    {
        return new self("Webhook payload is not a valid JSON object.", 0, $previous);
    }

    public static function unknownEventType(string $type): self
    {
        return new self("Unknown webhook event type \"{$type}\".");
    }
}
