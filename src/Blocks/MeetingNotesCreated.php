<?php

namespace Notion\Blocks;

/**
 * Response of a meeting notes creation.
 *
 * Processing continues asynchronously after creation. Use
 * `Blocks\Client::find()` with `$id` to follow the processing status.
 *
 * @psalm-immutable
 */
final readonly class MeetingNotesCreated
{
    private function __construct(
        /** ID of the created meeting notes block */
        public string $id,
        /**
         * Full meeting notes block.
         *
         * `null` when the integration lacks the "Read content" capability, in
         * which case Notion only returns the block ID.
         */
        public BlockInterface|null $block,
    ) {
    }

    /**
     * @internal
     *
     * @param array{ object: string, id: string, type?: string, ... } $array
     */
    public static function fromArray(array $array): self
    {
        $id = $array["id"];
        $block = null;
        if (isset($array["type"])) {
            /** @psalm-var array{ type: string, ... } $array */
            $block = BlockFactory::fromArray($array);
        }

        return new self($id, $block);
    }
}
