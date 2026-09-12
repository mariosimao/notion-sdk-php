<?php

namespace Notion\Blocks;

/**
 * @psalm-type SyncedFromJson = array{
 *     type?: string,
 *     block_id: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class SyncedFrom
{
    /** @psalm-mutation-free */
    private function __construct(
        public string $blockId,
        public string $type = "block_id",
    ) {
    }

    /** @psalm-mutation-free */
    public static function create(string $blockId): self
    {
        return new self($blockId);
    }

    /**
     * @psalm-param SyncedFromJson $array
     * @psalm-mutation-free
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["block_id"],
            $array["type"] ?? "block_id",
        );
    }

    /** @psalm-mutation-free */
    public function toArray(): array
    {
        return [
            "type"     => $this->type,
            "block_id" => $this->blockId,
        ];
    }

    /** @psalm-mutation-free */
    public function changeBlockId(string $blockId): self
    {
        return new self($blockId, $this->type);
    }
}
