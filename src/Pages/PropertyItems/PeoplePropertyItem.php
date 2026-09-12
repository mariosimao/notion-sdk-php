<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type PeopleItemJson = array{
 *      id: string,
 *      type: "people",
 *      people: UserJson|list<UserJson>,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class PeoplePropertyItem implements PropertyItemInterface
{
    /**
     * @param list<User> $people
     */
    private function __construct(
        private PropertyItemMetadata $metadata,
        public array $people,
        public User|null $user = null,
    ) {
    }

    public static function create(User $user, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::People);

        return new self($metadata, [$user], $user);
    }

    /**
     * @param list<User> $people
     */
    public static function createMultiple(array $people, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::People);
        $user = $people[0] ?? null;

        return new self($metadata, $people, $user);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var PeopleItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);

        /** @var UserJson|list<UserJson> $rawPeople */
        $rawPeople = $array["people"];

        if (isset($rawPeople[0]) || empty($rawPeople)) {
            /** @var list<UserJson> $rawPeople */
            $people = array_map(fn(array $u) => User::fromArray($u), $rawPeople);
            $user = $people[0] ?? null;
        } else {
            /** @var UserJson $rawPeople */
            $singleUser = User::fromArray($rawPeople);
            $people = [$singleUser];
            $user = $singleUser;
        }

        return new self($metadata, $people, $user);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        if (count($this->people) === 1 && $this->user !== null) {
            $array["people"] = $this->user->toArray();
        } else {
            $array["people"] = array_map(fn(User $u) => $u->toArray(), $this->people);
        }

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return empty($this->people);
    }
}
