<?php

namespace Notion\Sessions;

/**
 * Models used by the agent in a session.
 *
 * @psalm-type ModelSelectionJson = array{
 *      type: "auto"|"pinned",
 *      ids?: list<string|null>,
 * }
 *
 * @psalm-immutable
 */
final readonly class ModelSelection
{
    /** @param list<string|null> $ids */
    private function __construct(
        public ModelSelectionType $type,
        public array $ids,
    ) {
    }

    /**
     * @param ModelSelectionJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(ModelSelectionType::from($array["type"]), $array["ids"] ?? []);
    }

    /** @return ModelSelectionJson */
    public function toArray(): array
    {
        if ($this->type === ModelSelectionType::Auto) {
            return [ "type" => "auto" ];
        }

        return [
            "type" => "pinned",
            "ids" => $this->ids,
        ];
    }
}
