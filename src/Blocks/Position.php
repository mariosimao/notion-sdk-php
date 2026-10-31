<?php

namespace Notion\Blocks;

/**
 * Where new blocks are inserted when appending children to a block.
 *
 * @psalm-type PositionJson = array{
 *      type: string,
 *      after_block?: array{ id: string },
 * }
 *
 * @psalm-immutable
 */
final readonly class Position
{
    private function __construct(
        public PositionType $type,
        public string|null $afterBlockId,
    ) {
    }

    /** Insert at the beginning of the parent block. */
    public static function start(): self
    {
        return new self(PositionType::Start, null);
    }

    /** Insert at the end of the parent block (API default). */
    public static function end(): self
    {
        return new self(PositionType::End, null);
    }

    /** Insert right after an existing child block. */
    public static function afterBlock(string $blockId): self
    {
        return new self(PositionType::AfterBlock, $blockId);
    }

    /** @psalm-return PositionJson */
    public function toArray(): array
    {
        $array = [ "type" => $this->type->value ];

        if ($this->afterBlockId !== null) {
            $array["after_block"] = [ "id" => $this->afterBlockId ];
        }

        return $array;
    }
}
