<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type SyncedFromJson from SyncedFrom
 *
 * @psalm-type SyncedBlockJson = array{
 *     synced_block: array{
 *         synced_from: SyncedFromJson|null,
 *         children?: list<array{ type: string, ... }>,
 *     },
 * }
 *
 * @psalm-immutable
 */
final readonly class SyncedBlock implements BlockInterface
{
    /**
     * @param BlockInterface[] $children
     */
    private function __construct(
        private BlockMetadata $metadata,
        public SyncedFrom|null $syncedFrom,
        public array $children,
    ) {
        $metadata->checkType(BlockType::SyncedBlock);

        if ($this->syncedFrom !== null && count($children) > 0) {
            throw BlockException::noChindrenSupport();
        }
    }

    public static function createOriginal(BlockInterface ...$children): self
    {
        $hasChildren = count($children) > 0;
        $metadata = BlockMetadata::create(BlockType::SyncedBlock)->updateHasChildren($hasChildren);

        return new self($metadata, null, $children);
    }

    public static function createReference(string|BlockInterface $block): self
    {
        $blockId = match (true) {
            $block instanceof SyncedBlock && $block->isReference() => $block->syncedFrom->blockId,
            $block instanceof BlockInterface => $block->metadata()->id,
            default => $block,
        };

        $metadata = BlockMetadata::create(BlockType::SyncedBlock);

        return new self($metadata, SyncedFrom::create($blockId), []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var SyncedBlockJson $array */
        $syncedBlock = $array["synced_block"];

        $syncedFromArray = $syncedBlock["synced_from"] ?? null;
        $syncedFrom = $syncedFromArray !== null ? SyncedFrom::fromArray($syncedFromArray) : null;

        $rawChildren = $syncedBlock["children"] ?? [];
        $children = array_map(fn(array $child) => BlockFactory::fromArray($child), $rawChildren);

        return new self($metadata, $syncedFrom, $children);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        if ($this->isReference()) {
            $array["synced_block"] = [
                "synced_from" => $this->syncedFrom->toArray(),
            ];
        } else {
            $array["synced_block"] = [
                "synced_from" => null,
                "children"    => array_map(fn(BlockInterface $b) => $b->toArray(), $this->children),
            ];
        }

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    /**
     * @psalm-assert-if-true null $this->syncedFrom
     */
    public function isOriginal(): bool
    {
        return $this->syncedFrom === null;
    }

    /**
     * @psalm-assert-if-true SyncedFrom $this->syncedFrom
     */
    public function isReference(): bool
    {
        return $this->syncedFrom !== null;
    }

    public function originalBlockId(): string|null
    {
        return $this->syncedFrom?->blockId;
    }

    public function addChild(BlockInterface $child): self
    {
        if ($this->isReference()) {
            throw BlockException::noChindrenSupport();
        }

        $children = $this->children;
        $children[] = $child;

        return new self(
            $this->metadata->updateHasChildren(true),
            null,
            $children,
        );
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        if ($this->isReference()) {
            throw BlockException::noChindrenSupport();
        }

        $hasChildren = count($children) > 0;

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            null,
            $children,
        );
    }

    public function changeSyncedFrom(SyncedFrom|string $syncedFrom): self
    {
        $syncedFrom = is_string($syncedFrom) ? SyncedFrom::create($syncedFrom) : $syncedFrom;

        return new self(
            $this->metadata->update(),
            $syncedFrom,
            [],
        );
    }

    public function toOriginal(BlockInterface ...$children): self
    {
        $hasChildren = count($children) > 0;

        return new self(
            $this->metadata->updateHasChildren($hasChildren),
            null,
            $children,
        );
    }

    public function toReference(SyncedFrom|string|BlockInterface $syncedFrom): self
    {
        $blockId = match (true) {
            $syncedFrom instanceof SyncedBlock && $syncedFrom->isReference() => $syncedFrom->syncedFrom->blockId,
            $syncedFrom instanceof BlockInterface => $syncedFrom->metadata()->id,
            $syncedFrom instanceof SyncedFrom => $syncedFrom->blockId,
            default => $syncedFrom,
        };

        return new self(
            $this->metadata->updateHasChildren(false),
            SyncedFrom::create($blockId),
            [],
        );
    }

    public function delete(): BlockInterface
    {
        return new self(
            $this->metadata->delete(),
            $this->syncedFrom,
            $this->children,
        );
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
