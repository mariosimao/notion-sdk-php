<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type CreatedByItemJson = array{
 *      id: string,
 *      type: "created_by",
 *      created_by: UserJson,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class CreatedByPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public User $user,
    ) {
    }

    public static function create(User $user, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::CreatedBy);

        return new self($metadata, $user);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CreatedByItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $user = User::fromArray($array["created_by"]);

        return new self($metadata, $user);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["created_by"] = $this->user->toArray();

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }
}
