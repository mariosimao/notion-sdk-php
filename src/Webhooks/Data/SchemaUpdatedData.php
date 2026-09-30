<?php

namespace Notion\Webhooks\Data;

use Notion\Webhooks\EventParent;

/**
 * Data of `database.schema_updated` and `data_source.schema_updated` events.
 *
 * @psalm-import-type EventParentJson from EventParent
 * @psalm-import-type PropertyChangeJson from PropertyChange
 *
 * @psalm-type SchemaUpdatedDataJson = array{
 *     parent: EventParentJson,
 *     updated_properties?: list<PropertyChangeJson>,
 * }
 *
 * @psalm-immutable
 */
final readonly class SchemaUpdatedData implements EventData
{
    /** @param list<PropertyChange> $updatedProperties */
    private function __construct(
        public EventParent $parent,
        public array $updatedProperties,
    ) {
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var SchemaUpdatedDataJson $array */
        return new self(
            EventParent::fromArray($array["parent"]),
            array_map(PropertyChange::fromArray(...), $array["updated_properties"] ?? []),
        );
    }
}
