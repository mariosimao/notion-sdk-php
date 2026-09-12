<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type LastEditedByItemJson = array{
 *      id: string,
 *      type: "last_edited_by",
 *      last_edited_by: UserJson,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class LastEditedByPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public User $user,
    ) {
    }

    public static function create(User $user, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::LastEditedBy);

        return new self($metadata, $user);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var LastEditedByItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $user = User::fromArray($array["last_edited_by"]);

        return new self($metadata, $user);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["last_edited_by"] = $this->user->toArray();

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }
}
