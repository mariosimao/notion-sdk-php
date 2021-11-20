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
    private const TYPE = Property::TYPE_LAST_EDITED_BY;

    private Property $property;

    private User $user;

    private function __construct(Property $property, User $user)
    {
        $this->property = $property;
        $this->user = $user;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function user(): User
    {
        return $this->user;
    }

    public static function fromArray(array $array): self
    {
        /** @var LastEditedByJson $array */

        $property = Property::fromArray($array);
        $user = User::fromArray($array[self::TYPE]);

        return new self($property, $user);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = $this->user->toArray();

        return $array;
    }
}
