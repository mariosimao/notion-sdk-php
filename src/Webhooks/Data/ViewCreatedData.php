<?php

namespace Notion\Webhooks\Data;

use Notion\Webhooks\EventParent;

/**
 * Data of `view.created` events.
 *
 * @psalm-import-type EventParentJson from EventParent
 *
 * @psalm-type ViewCreatedDataJson = array{
 *     parent: EventParentJson,
 *     view_type: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class ViewCreatedData implements EventData
{
    /** @param string $viewType For example `table`, `board`, `list`, `calendar`, `gallery` or `timeline`. */
    private function __construct(
        public EventParent $parent,
        public string $viewType,
    ) {
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var ViewCreatedDataJson $array */
        return new self(
            EventParent::fromArray($array["parent"]),
            $array["view_type"],
        );
    }
}
