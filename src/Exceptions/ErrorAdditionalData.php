<?php

namespace Notion\Exceptions;

/**
 * Additional data sent by the Notion API in error responses (`additional_data`)
 *
 * @psalm-type ErrorAdditionalDataJson = array<string, mixed>
 *
 * @psalm-immutable
 */
final readonly class ErrorAdditionalData
{
    /**
     * @param list<string> $retryGuidance
     * @param list<string> $committedChildIds
     * @param array<string, mixed> $raw
     */
    private function __construct(
        public int|null $retryAfter,
        public string|null $rateLimitReason,
        public array $retryGuidance,
        public string|null $committedResourceId,
        public array $committedChildIds,
        public array $raw,
    ) {
    }

    public static function empty(): self
    {
        return new self(null, null, [], null, [], []);
    }

    /**
     * @psalm-suppress MixedAssignment
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        /** @var array<string, mixed> $raw */
        $raw = array_filter($array, fn ($key) => is_string($key), ARRAY_FILTER_USE_KEY);

        return new self(
            retryAfter: self::parseSeconds($raw["retry_after"] ?? null),
            rateLimitReason: self::stringOrNull($raw["rate_limit_reason"] ?? null),
            retryGuidance: self::stringList($raw["retry_guidance"] ?? null),
            committedResourceId: self::stringOrNull($raw["committed_resource_id"] ?? null),
            committedChildIds: self::stringList($raw["committed_child_ids"] ?? null),
            raw: $raw,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->raw;
    }

    /** Whether the connection's API access has been restricted, so retrying won't help. */
    public function isRequestBlocked(): bool
    {
        return $this->rateLimitReason === "public_api_request_blocked";
    }

    /** @internal */
    public static function parseSeconds(mixed $value): int|null
    {
        if (is_int($value)) {
            return $value >= 0 ? $value : null;
        }

        if (is_string($value) && preg_match("/^\s*\d+\s*$/", $value) === 1) {
            return (int) trim($value);
        }

        return null;
    }

    private static function stringOrNull(mixed $value): string|null
    {
        return is_string($value) ? $value : null;
    }

    /** @return list<string> */
    private static function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, "is_string"));
    }
}
