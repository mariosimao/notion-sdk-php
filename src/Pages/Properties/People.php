<?php

namespace Notion\Pages\Properties;

use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type PeopleJson = array{
 *      id: string,
 *      type: "people",
 *      people: UserJson[],
 * }
 *
 * @psalm-immutable
 */
class People implements PropertyInterface
{
    /** @param User[] $users */
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly array $users
    ) {
    }

    public static function create(User ...$users): self
    {
        $property = PropertyMetadata::create("", PropertyType::People);

        return new self($property, $users);
    }

    public static function createEmpty(string $id = null): self
    {
        $property = PropertyMetadata::create($id ?? "", PropertyType::People);

        return new self($property, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var PeopleJson $array */

        $property = PropertyMetadata::fromArray($array);

        $users = array_map(
            function (array $userArray): User {
                return User::fromArray($userArray);
            },
            $array["people"],
        );

        return new self($property, $users);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["people"] = array_map(
            function (User $user): array {
                return $user->toArray();
            },
            $this->users,
        );

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changePeople(User ...$users): self
    {
        return new self($this->metadata, $users);
    }

    public function addPerson(User $user): self
    {
        $users = $this->users;
        $users[] = $user;

        return new self($this->metadata, $users);
    }

    public function removePerson(string $userId): self
    {
        return new self(
            $this->metadata,
            array_filter($this->users, fn (User $u) => $u->id !== $userId),
        );
    }
}
