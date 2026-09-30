<?php

namespace Notion\Webhooks\Data;

use Notion\Webhooks\EventParent;

/**
 * Data of `page.properties_updated` events.
 *
 * @psalm-import-type EventParentJson from EventParent
 *
 * @psalm-type PagePropertiesUpdatedDataJson = array{
 *     parent: EventParentJson,
 *     updated_properties: list<string>,
 * }
 *
 * @psalm-immutable
 */
final readonly class PagePropertiesUpdatedData implements EventData
{
    /** @param list<string> $updatedProperties IDs of the updated properties. */
    private function __construct(
        public EventParent $parent,
        public array $updatedProperties,
    ) {
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var PagePropertiesUpdatedDataJson $array */
        return new self(
            EventParent::fromArray($array["parent"]),
            $array["updated_properties"],
        );
    }
}
