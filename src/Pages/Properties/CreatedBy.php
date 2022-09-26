<?php

namespace Notion\Pages\Properties;

use Notion\Users\User;

/**
 * @psalm-import-type UserJson from \Notion\Users\User
 *
 * @psalm-type CreatedByJson = array{
 *      id: string,
 *      type: "created_by",
 *      "created_by": UserJson,
 * }
 *
 * @psalm-immutable
 */
class CreatedBy implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly User $user,
    ) {
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var CreatedByJson $array */
        $property = PropertyMetadata::fromArray($array);
        $user = User::fromArray($array["created_by"]);

        return new self($property, $user);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["created_by"] = $this->user->toArray();

        return $array;
    }
}
