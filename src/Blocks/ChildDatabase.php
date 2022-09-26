<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\RichText;
use Notion\NotionException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type ChildDatabaseJson = array{
 *      child_database: array{ title: string },
 * }
 *
 * @psalm-immutable
 */
class ChildDatabase implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly string $databaseTitle
    ) {
        $metadata->checkType(BlockType::ChildDatabase);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var ChildDatabaseJson $array */
        $databaseTitle = $array["child_database"]["title"];

        return new self($metadata, $databaseTitle);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["child_database"] = [ "title" => $this->databaseTitle ];

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            "child_database" => [
                "title" => $this->databaseTitle,
            ],
            "archived" => $this->metadata()->archived,
        ];
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function addChild(BlockInterface $child): self
    {
        throw BlockException::noChindrenSupport();
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        throw BlockException::noChindrenSupport();
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->databaseTitle,
        );
    }
}
