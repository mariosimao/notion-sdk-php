<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type ChildPageJson = array{
 *      child_page: array{ title: string },
 * }
 *
 * @psalm-immutable
 */
class ChildPage implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly string $title,
    ) {
        $metadata->checkType(BlockType::ChildPage);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var ChildPageJson $array */
        $pageTitle = $array["child_page"]["title"];

        return new self($metadata, $pageTitle);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["child_page"] = [ "title" => $this->title ];

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function addChild(BlockInterface $child): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function changeChildren(BlockInterface ...$children): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->title,
        );
    }
}
