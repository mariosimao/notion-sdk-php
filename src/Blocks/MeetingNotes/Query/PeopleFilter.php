<?php

namespace Notion\Blocks\MeetingNotes\Query;

use InvalidArgumentException;
use Notion\Users\User;

/**
 * Filter meeting notes by people (attendees, creator or last editor)
 *
 * @psalm-immutable
 */
final readonly class PeopleFilter implements PropertyFilter
{
    public const MAX_USERS = 100;

    private const VALID_PROPERTIES = [
        Property::Attendees,
        Property::CreatedBy,
        Property::LastEditedBy,
    ];

    private const VALID_OPERATORS = [
        Operator::PersonContains,
        Operator::PersonDoesNotContain,
        Operator::IsEmpty,
        Operator::IsNotEmpty,
    ];

    /** @param UserReference[] $users */
    private function __construct(
        private Property $property,
        private Operator $operator,
        private array $users,
    ) {
        if (!in_array($property, self::VALID_PROPERTIES, true)) {
            throw new InvalidArgumentException("Invalid property");
        }

        if (!in_array($operator, self::VALID_OPERATORS, true)) {
            throw new InvalidArgumentException("Invalid operator");
        }
    }

    public static function attendees(): self
    {
        return new self(Property::Attendees, Operator::IsNotEmpty, []);
    }

    public static function createdBy(): self
    {
        return new self(Property::CreatedBy, Operator::IsNotEmpty, []);
    }

    public static function lastEditedBy(): self
    {
        return new self(Property::LastEditedBy, Operator::IsNotEmpty, []);
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function operator(): Operator
    {
        return $this->operator;
    }

    /** @return UserReference[] */
    public function users(): array
    {
        return $this->users;
    }

    /**
     * Match meeting notes containing any of the given users
     *
     * Users can be given as `UserReference`, `User` or user ID (plain UUID
     * or `user://<uuid>`).
     */
    public function contains(UserReference|User|string ...$users): self
    {
        return new self($this->property, Operator::PersonContains, self::normalizeUsers($users));
    }

    /**
     * Match meeting notes not containing the given users
     *
     * Users can be given as `UserReference`, `User` or user ID (plain UUID
     * or `user://<uuid>`).
     */
    public function doesNotContain(UserReference|User|string ...$users): self
    {
        return new self($this->property, Operator::PersonDoesNotContain, self::normalizeUsers($users));
    }

    /** Match meeting notes containing the user tied to the integration */
    public function containsMe(): self
    {
        return $this->contains(UserReference::me());
    }

    public function isEmpty(): self
    {
        return new self($this->property, Operator::IsEmpty, []);
    }

    public function isNotEmpty(): self
    {
        return new self($this->property, Operator::IsNotEmpty, []);
    }

    public function toArray(): array
    {
        $filter = [ "operator" => $this->operator->value ];

        if ($this->operator !== Operator::IsEmpty && $this->operator !== Operator::IsNotEmpty) {
            $filter["value"] = array_map(fn(UserReference $u) => $u->toArray(), $this->users);
        }

        return [
            "property" => $this->property->value,
            "filter"   => $filter,
        ];
    }

    /**
     * @param array<UserReference|User|string> $users
     *
     * @return list<UserReference>
     *
     * @psalm-pure
     */
    private static function normalizeUsers(array $users): array
    {
        $normalized = [];
        foreach ($users as $user) {
            $reference = UserReference::normalize($user);
            $key = $reference->userId ?? "me";
            $normalized[$key] = $reference;
        }

        if (count($normalized) === 0) {
            throw new InvalidArgumentException("At least one user is required.");
        }

        if (count($normalized) > self::MAX_USERS) {
            throw new InvalidArgumentException("Maximum number of users: " . self::MAX_USERS);
        }

        return array_values($normalized);
    }
}
