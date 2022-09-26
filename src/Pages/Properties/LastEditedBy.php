<?php

namespace Notion\Pages\Properties;

use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type LastEditedByJson = array{
 *      id: string,
 *      type: "last_edited_by",
 *      "last_edited_by": UserJson,
 * }
 *
 * @psalm-immutable
 */
class LastEditedBy implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly User $user
    ) {
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var LastEditedByJson $array */

        $property = PropertyMetadata::fromArray($array);
        $user = User::fromArray($array["last_edited_by"]);

        return new self($property, $user);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["last_edited_by"] = $this->user->toArray();

        return $array;
    }
}
