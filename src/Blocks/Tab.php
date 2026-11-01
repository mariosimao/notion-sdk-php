<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException\TabException;

/**
 * Tabbed container block.
 *
 * Each tab is a paragraph child: the paragraph text is the tab label, the
 * paragraph icon is the tab icon and the paragraph children are the tab content.
 *
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 *
 * @psalm-type TabJson = array{
 *     tab: array{
 *         children?: list<BlockMetadataJson>
 *     },
 * }
 *
 * @psalm-immutable
 */
final readonly class Tab implements BlockInterface
{
    /** @param Paragraph[] $tabs */
    private function __construct(
        private BlockMetadata $metadata,
        public array $tabs,
    ) {
        $metadata->checkType(BlockType::Tab);
    }

    public static function create(Paragraph ...$tabs): self
    {
        $metadata = BlockMetadata::create(BlockType::Tab)
            ->updateHasChildren(count($tabs) > 0);

        return new self($metadata, $tabs);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var TabJson $array */
        $rawTabs = $array["tab"]["children"] ?? [];
        $tabs = array_map(fn($child) => BlockFactory::fromArray($child), $rawTabs);

        self::checkChildren(...$tabs);

        /** @var Paragraph[] $tabs */
        return new self($metadata, $tabs);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["tab"] = [
            "children" => array_map(fn (Paragraph $p) => $p->toArray(), $this->tabs),
        ];

        return $array;
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function addTab(Paragraph $tab): self
    {
        return new self(
            $this->metadata->updateHasChildren(true),
            [...$this->tabs, $tab],
        );
    }

    public function changeTabs(Paragraph ...$tabs): self
    {
        return new self(
            $this->metadata->updateHasChildren(count($tabs) > 0),
            $tabs,
        );
    }

    /** @throws TabException When child is not a paragraph. */
    public function addChild(BlockInterface $child): self
    {
        self::checkChildren($child);

        /** @var Paragraph $child */
        return $this->addTab($child);
    }

    /** @throws TabException When any child is not a paragraph. */
    public function changeChildren(BlockInterface ...$children): self
    {
        self::checkChildren(...$children);

        /** @var Paragraph[] $children */
        return $this->changeTabs(...$children);
    }

    public function delete(): BlockInterface
    {
        return new self(
            $this->metadata->delete(),
            $this->tabs,
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

    /** @psalm-pure */
    private static function checkChildren(BlockInterface ...$children): void
    {
        foreach ($children as $child) {
            if (!$child instanceof Paragraph) {
                throw TabException::childNotParagraph();
            }
        }
    }
}
