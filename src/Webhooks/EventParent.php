<?php

namespace Notion\Webhooks;

/**
 * @psalm-type EventParentJson = array{
 *     id: string,
 *     type: string,
 *     data_source_id?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class EventParent
{
    private function __construct(
        public string $id,
        public EventParentType $type,
        public string|null $dataSourceId,
    ) {
    }

    /**
     * @psalm-param EventParentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            EventParentType::from($array["type"]),
            $array["data_source_id"] ?? null,
        );
    }
}
