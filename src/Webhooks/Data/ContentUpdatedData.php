<?php

namespace Notion\Webhooks\Data;

use Notion\Webhooks\Entity;
use Notion\Webhooks\EventParent;

/**
 * Data of `page.content_updated`, `database.content_updated` and `data_source.content_updated` events.
 *
 * @psalm-import-type EventParentJson from EventParent
 * @psalm-import-type EntityJson from Entity
 *
 * @psalm-type ContentUpdatedDataJson = array{
 *     parent: EventParentJson,
 *     updated_blocks: list<EntityJson>,
 * }
 *
 * @psalm-immutable
 */
final readonly class ContentUpdatedData implements EventData
{
    /** @param list<Entity> $updatedBlocks */
    private function __construct(
        public EventParent $parent,
        public array $updatedBlocks,
    ) {
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var ContentUpdatedDataJson $array */
        return new self(
            EventParent::fromArray($array["parent"]),
            array_map(Entity::fromArray(...), $array["updated_blocks"]),
        );
    }
}
