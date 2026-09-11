<?php

namespace Notion\Authentication;

use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type OwnerJson = array{
 *     type: "user"|"workspace",
 *     user?: UserJson,
 *     workspace?: true,
 * }
 *
 * @psalm-immutable
 */
final readonly class Owner
{
    private function __construct(
        public OwnerType $type,
        public User|null $user = null,
    ) {
    }

    public static function user(User $user): self
    {
        return new self(OwnerType::User, $user);
    }

    public static function workspace(): self
    {
        return new self(OwnerType::Workspace, null);
    }

    /**
     * @psalm-param OwnerJson $array
     */
    public static function fromArray(array $array): self
    {
        $type = OwnerType::from($array["type"]);
        $user = isset($array["user"]) ? User::fromArray($array["user"]) : null;

        return new self($type, $user);
    }

    /**
     * @psalm-assert-if-true User $this->user
     */
    public function isUser(): bool
    {
        return $this->type === OwnerType::User;
    }

    public function isWorkspace(): bool
    {
        return $this->type === OwnerType::Workspace;
    }
}
