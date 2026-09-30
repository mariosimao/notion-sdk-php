<?php

namespace Notion\Webhooks\Data;

use Notion\Webhooks\Entity;

/**
 * Data of `page.transcription_block.transcript_deleted` events.
 *
 * @psalm-import-type EntityJson from Entity
 *
 * @psalm-type TranscriptDeletedDataJson = array{
 *     target: EntityJson,
 *     transcript_id: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class TranscriptDeletedData implements EventData
{
    /** @param Entity $target Block that contained the deleted transcript. */
    private function __construct(
        public Entity $target,
        public string|null $transcriptId,
    ) {
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var TranscriptDeletedDataJson $array */
        return new self(
            Entity::fromArray($array["target"]),
            $array["transcript_id"] ?? null,
        );
    }
}
