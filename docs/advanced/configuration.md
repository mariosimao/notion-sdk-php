# SDK Configuration

The SDK can be configured with custom options.

```php
$config = Configuration::create();

$notion = Notion::createFromConfig($config);
```

## Default values

| Option | Type | Default |
|--------|------|---------|
|[`retryOnConflict`](#retry-on-conflict)|bool|`true`|
|[`retryOnConflictAttempts`](#retry-on-conflict)|int|`1`|
|[`retryOnRateLimit`](#retry-on-rate-limit)|bool|`true`|
|[`retryOnRateLimitAttempts`](#retry-on-rate-limit)|int|`3`|
|[`retryOnRateLimitMaxWait`](#retry-on-rate-limit)|int (seconds)|`60`|

## Retry on conflict

Sometimes, the Notion API responds with the following error

```json
{
    "code": "conflict_error",
    "message": "Conflict occurred while saving. Please try again."
}
```

The SDK provides a retry option (enabled by default) and sends the request again
until a success responde or when reaches the maximum number of attempts.

### Enable

```php
$token = $_ENV["NOTION_TOKEN"];

$retryAttempts = 3;
$config = Configuration::create($token)
            ->enableRetryOnConflict($retryAttempts);

$notion = Notion::createFromConfig($config);
```

### Disable

```php
$token = $_ENV["NOTION_TOKEN"];

$config = Configuration::create($token)
            ->disableRetryOnConflict();

$notion = Notion::createFromConfig($config);
```

## Retry on rate limit

When a request exceeds a [Notion rate limit](https://developers.notion.com/reference/request-limits),
the API responds with HTTP 429 and the following error:

```json
{
    "code": "rate_limited",
    "message": "You have been rate limited. Please try again later.",
    "additional_data": {
        "retry_after": "30",
        "rate_limit_reason": "public_api_request_rate_limit"
    }
}
```

By default, the SDK waits the number of seconds in `additional_data.retry_after`
(falling back to the `Retry-After` header, and to exponential backoff when
neither is available), capped at the maximum wait, and sends the request again.
Requests blocked by Notion (`public_api_request_blocked`) are never retried.

### Enable

```php
$token = $_ENV["NOTION_TOKEN"];

$retryAttempts = 5;
$maxWaitSeconds = 30;
$config = Configuration::create($token)
            ->enableRetryOnRateLimit($retryAttempts, $maxWaitSeconds);

$notion = Notion::createFromConfig($config);
```

### Disable

```php
$token = $_ENV["NOTION_TOKEN"];

$config = Configuration::create($token)
            ->disableRetryOnRateLimit();

$notion = Notion::createFromConfig($config);
```

### Handling rate limit errors

When retries are disabled or exhausted, a `RateLimitedException` is thrown with
the error metadata:

```php
use Notion\Exceptions\RateLimitedException;

try {
    $notion->pages()->find($pageId);
} catch (RateLimitedException $e) {
    $e->retryAfter();        // seconds to wait, e.g. 30
    $e->rateLimitReason();   // e.g. "public_api_request_rate_limit"
    $e->isRetryable();       // false for "public_api_request_blocked"
    $e->additionalData;      // Notion\Exceptions\ErrorAdditionalData
}
```
