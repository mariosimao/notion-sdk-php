<?php

namespace Notion\Blocks;

use Notion\Blocks\Exceptions\BlockException;
use Notion\Common\Equation;
use Notion\NotionException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type EquationJson from \Notion\Common\Equation
 *
 * @psalm-type EquationBlockMetadataJson = array{
 *      equation: EquationJson,
 * }
 *
 * @psalm-immutable
 */
class EquationBlock implements BlockInterface
{
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly Equation $equation
    ) {
        $metadata->checkType(BlockType::Equation);
    }

    public static function create(string $expression = ""): self
    {
        $block = BlockMetadata::create(BlockType::Equation);
        $equation = Equation::create($expression);

        return new self($block, $equation);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $block = BlockMetadata::fromArray($array);

        /** @psalm-var EquationBlockMetadataJson $array */
        $equation = Equation::fromArray($array["equation"]);

        return new self($block, $equation);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["equation"] = $this->equation->toArray();

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            "equation" => $this->equation->toArray(),
            "archived" => $this->metadata()->archived,
        ];
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function changeEquation(Equation $equation): self
    {
        return new self($this->metadata, $equation);
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
            $this->equation,
        );
    }
}
