<?php

namespace Notion\Webhooks\Data;

use Notion\Webhooks\EventParent;

/**
 * Data of `view.updated` events.
 *
 * @psalm-import-type EventParentJson from EventParent
 *
 * @psalm-type ViewUpdatedDataJson = array{
 *     parent: EventParentJson,
 *     updated_fields: list<string>,
 * }
 *
 * @psalm-immutable
 */
final readonly class ViewUpdatedData implements EventData
{
    /** @param list<string> $updatedFields For example `name`, `filter`, `sorts` or `configuration`. */
    private function __construct(
        public EventParent $parent,
        public array $updatedFields,
    ) {
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var ViewUpdatedDataJson $array */
        return new self(
            EventParent::fromArray($array["parent"]),
            $array["updated_fields"],
        );
    }
}
