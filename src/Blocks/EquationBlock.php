<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\Equation;

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

    public static function fromString(string $expression = ""): self
    {
        $block = BlockMetadata::create(BlockType::Equation);
        $equation = Equation::fromString($expression);

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

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function changeEquation(Equation $equation): self
    {
        return new self($this->metadata, $equation);
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
            $this->equation,
        );
    }
}
