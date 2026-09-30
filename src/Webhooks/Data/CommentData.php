<?php

namespace Notion\Webhooks\Data;

use Notion\Webhooks\EventParent;

/**
 * Data of `comment.created`, `comment.updated` and `comment.deleted` events.
 *
 * @psalm-import-type EventParentJson from EventParent
 *
 * @psalm-type CommentDataJson = array{
 *     parent: EventParentJson,
 *     page_id: string,
 *     discussion_id?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class CommentData implements EventData
{
    private function __construct(
        public EventParent $parent,
        public string $pageId,
        public string|null $discussionId,
    ) {
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var CommentDataJson $array */
        return new self(
            EventParent::fromArray($array["parent"]),
            $array["page_id"],
            $array["discussion_id"] ?? null,
        );
    }
}
