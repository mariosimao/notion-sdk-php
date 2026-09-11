<?php

namespace Notion\Authentication;

/**
 * @psalm-type TokenIntrospectionJson = array{
 *     active: bool,
 *     scope?: string|null,
 *     iat?: int|null,
 *     request_id?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class TokenIntrospection
{
    private function __construct(
        public bool $active,
        public string|null $scope = null,
        public int|null $iat = null,
        public string|null $requestId = null,
    ) {
    }

    public static function create(
        bool $active,
        string|null $scope = null,
        int|null $iat = null,
        string|null $requestId = null,
    ): self {
        return new self($active, $scope, $iat, $requestId);
    }

    /**
     * @psalm-param TokenIntrospectionJson $array
     */
    public static function fromArray(array $array): self
    {
        return new self(
            active: $array["active"],
            scope: $array["scope"] ?? null,
            iat: $array["iat"] ?? null,
            requestId: $array["request_id"] ?? null,
        );
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
