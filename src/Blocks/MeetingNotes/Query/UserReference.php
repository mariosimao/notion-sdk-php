<?php

namespace Notion\Blocks\MeetingNotes\Query;

use InvalidArgumentException;
use Notion\Users\User;

/**
 * Reference to a Notion user used by people filters (attendees, created by
 * and last edited by)
 *
 * @psalm-immutable
 */
final readonly class UserReference
{
    private const USER_URI_PREFIX = "user://";

    private function __construct(
        public string|null $userId,
    ) {
    }

    /**
     * Reference a user by ID
     *
     * Accepts both the plain UUID and the `user://<uuid>` form returned by
     * user search. The ID is normalized to the plain UUID.
     *
     * @psalm-pure
     */
    public static function id(string $userId): self
    {
        $userId = trim($userId);
        if (str_starts_with($userId, self::USER_URI_PREFIX)) {
            $userId = substr($userId, strlen(self::USER_URI_PREFIX));
        }

        if ($userId === "") {
            throw new InvalidArgumentException("User ID cannot be empty.");
        }

        return new self($userId);
    }

    /** @psalm-pure */
    public static function user(User $user): self
    {
        return self::id($user->id);
    }

    /**
     * User tied to the integration
     *
     * @psalm-pure
     */
    public static function me(): self
    {
        return new self(null);
    }

    /**
     * @internal
     *
     * @psalm-pure
     */
    public static function normalize(self|User|string $user): self
    {
        if ($user instanceof self) {
            return $user;
        }

        if ($user instanceof User) {
            return self::user($user);
        }

        return self::id($user);
    }

    public function isMe(): bool
    {
        return $this->userId === null;
    }

    public function toArray(): array
    {
        if ($this->userId === null) {
            return [
                "type"  => "relative",
                "value" => "me",
            ];
        }

        return [
            "type"  => "exact",
            "value" => [
                "table" => "notion_user",
                "id"    => $this->userId,
            ],
        ];
    }
}
