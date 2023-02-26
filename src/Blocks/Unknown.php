<?php

namespace Notion\Blocks;

/**
 * An unknown block not implemented yet by the SDK.
 *
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-immutable
 */
class Unknown implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        private readonly array $data,
    ) {
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function addChild(BlockInterface $child): self
    {
        /** @var array{ children?: array } */
        $children = $this->data["children"] ?? [];
        $children[] = $child->toArray();

        $data = $this->data;
        $data["children"] = $children;

        return new self(
            $this->metadata->updateHasChildren(true),
            $data,
        );
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        $data = $this->data;
        $data["children"] = array_map(fn (BlockInterface $b) => $b->toArray(), $children);

        $hasChildren = (count($children) > 0);

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            $data,
        );
    }

    public function archive(): self
    {
        $metadata = $this->metadata()->archive();

        $data = array_merge($this->data, $metadata->toArray());

        return new self($metadata, $data);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        return new self($metadata, $array);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
