<?php

namespace Notion\Webhooks\Data;

/**
 * @psalm-type PropertyChangeJson = array{
 *     id: string,
 *     name: string|null,
 *     action: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class PropertyChange
{
    private function __construct(
        public string $id,
        public string|null $name,
        public PropertyChangeAction $action,
    ) {
    }

    /**
     * @psalm-param PropertyChangeJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            $array["name"],
            PropertyChangeAction::from($array["action"]),
        );
    }
}
