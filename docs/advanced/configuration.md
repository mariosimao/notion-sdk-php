# SDK Configuration

The SDK can be configured with custom options.

```php
$config = Configuration::create();

$notion = Notion::createFromConfig($config);
```

## Default values

| Option | Type | Default |
|--------|------|---------|
|[`retryPolicy->maxRetries`](#automatic-retries)|int|`2`|
|[`retryPolicy->initialDelayMs`](#automatic-retries)|int|`1000`|
|[`retryPolicy->maxDelayMs`](#automatic-retries)|int|`60000`|

## Automatic retries

The SDK retries failed requests following
[Notion's retry guidance](https://developers.notion.com/reference/request-limits#retry-rate-limited-requests).

Retried errors:

| Error | HTTP status | Retried methods |
|-------|-------------|-----------------|
| `rate_limited` | 429 | All (except `public_api_request_blocked`) |
| `service_overload` | 529 | All |
| `conflict_error` | 409 | All |
| Server errors | 500, 502, 503, 504 | `GET` and `DELETE` only |

Server errors are only retried for idempotent requests to avoid repeating writes.

The SDK waits for the `Retry-After` response header when present (seconds or
HTTP date). Otherwise, it uses exponential backoff starting at
`initialDelayMs`. A random jitter is added to every delay, and no delay exceeds
`maxDelayMs`.

### Customize

```php
use Notion\Configuration;
use Notion\Notion;
use Notion\RetryPolicy;

$token = $_ENV["NOTION_TOKEN"];

$config = Configuration::create($token)
    ->withRetryPolicy(RetryPolicy::create(
        maxRetries: 5,
        initialDelayMs: 500,
        maxDelayMs: 30000,
    ));

$notion = Notion::createFromConfig($config);
```

### Disable

```php
$token = $_ENV["NOTION_TOKEN"];

$config = Configuration::create($token)->withoutRetries();

$notion = Notion::createFromConfig($config);
```
