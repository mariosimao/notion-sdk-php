<?php

namespace Notion\Sessions;

/**
 * @psalm-type SessionCreatorJson = array{
 *      id: string,
 *      type: "user"|"bot",
 * }
 *
 * @psalm-immutable
 */
final readonly class SessionCreator
{
    private function __construct(
        public string $id,
        public CreatorType $type,
    ) {
    }

    /**
     * @param SessionCreatorJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["id"], CreatorType::from($array["type"]));
    }

    /** @return SessionCreatorJson */
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "type" => $this->type->value,
        ];
    }
}
