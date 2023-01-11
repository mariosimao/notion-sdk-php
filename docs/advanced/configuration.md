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
