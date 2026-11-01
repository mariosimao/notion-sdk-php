<?php

namespace Notion\Comments;

/**
 * @psalm-type CommentDisplayNameJson = array{
 *      type: "connection"|"user"|"custom",
 *      resolved_name?: string|null,
 *      custom?: array{ name: string }
 * }
 *
 * @psalm-immutable
 */
final readonly class CommentDisplayName
{
    private function __construct(
        public CommentDisplayNameType $type,
        public string|null $resolvedName,
    ) {
    }

    /** Display the name of the connection */
    public static function connection(): self
    {
        return new self(CommentDisplayNameType::Connection, null);
    }

    /** Display the name of the user who authenticated the connection or token */
    public static function user(): self
    {
        return new self(CommentDisplayNameType::User, null);
    }

    /** Display a custom name, useful for automated comments */
    public static function custom(string $name): self
    {
        return new self(CommentDisplayNameType::Custom, $name);
    }

    /**
     * @param CommentDisplayNameJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = CommentDisplayNameType::from($array["type"]);
        $resolvedName = $array["resolved_name"] ?? $array["custom"]["name"] ?? null;

        return new self($type, $resolvedName);
    }

    public function toArray(): array
    {
        return [
            "type" => $this->type->value,
            "resolved_name" => $this->resolvedName,
        ];
    }

    /** @internal */
    public function toRequestArray(): array
    {
        $array = [ "type" => $this->type->value ];

        if ($this->type === CommentDisplayNameType::Custom) {
            $array["custom"] = [ "name" => (string) $this->resolvedName ];
        }

        return $array;
    }

    public function isConnection(): bool
    {
        return $this->type === CommentDisplayNameType::Connection;
    }

    public function isUser(): bool
    {
        return $this->type === CommentDisplayNameType::User;
    }

    public function isCustom(): bool
    {
        return $this->type === CommentDisplayNameType::Custom;
    }
}
