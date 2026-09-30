<?php

namespace Notion\Webhooks\Data;

use Notion\Webhooks\EventParent;

/**
 * Data of events that only report the entity parent, such as `page.created` or `database.moved`.
 *
 * @psalm-import-type EventParentJson from EventParent
 *
 * @psalm-type ParentDataJson = array{
 *     parent: EventParentJson,
 * }
 *
 * @psalm-immutable
 */
final readonly class ParentData implements EventData
{
    private function __construct(
        public EventParent $parent,
    ) {
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var ParentDataJson $array */
        return new self(EventParent::fromArray($array["parent"]));
    }
}
