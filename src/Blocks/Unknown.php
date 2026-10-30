<?php

namespace Notion\Blocks;

/**
 * A block not implemented by the SDK or not supported by the Notion API.
 *
 * Blocks the Notion API does not support have type `unsupported` and expose
 * the underlying block type (e.g. `form`, `button`) in `$unsupportedBlockType`.
 * The raw payload is always preserved and returned by `toArray()`.
 *
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type UnsupportedJson = array{
 *      unsupported?: array{ block_type?: string },
 * }
 *
 * @psalm-immutable
 */
final readonly class Unknown implements BlockInterface
{
    private function __construct(
        private BlockMetadata $metadata,
        private array $data,
        public string|null $unsupportedBlockType = null,
    ) {
    }

    public function isUnsupported(): bool
    {
        return $this->metadata->type === BlockType::Unsupported;
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
            $this->unsupportedBlockType,
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
            $this->unsupportedBlockType,
        );
    }

    public function delete(): self
    {
        $metadata = $this->metadata()->delete();

        $data = array_merge($this->data, $metadata->toArray());

        return new self($metadata, $data, $this->unsupportedBlockType);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var UnsupportedJson $array */
        $unsupportedBlockType = $metadata->type === BlockType::Unsupported
            ? ($array["unsupported"]["block_type"] ?? null)
            : null;

        return new self($metadata, $array, $unsupportedBlockType);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @deprecated 1.17.0 Use `delete()` instead.
     * @codeCoverageIgnore
     */
    public function archive(): BlockInterface
    {
        return $this->delete();
    }
}
