<?php

namespace Notion\Blocks;

use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\Equation;
use Notion\NotionException;

/**
 * @psalm-import-type BlockJson from Block
 * @psalm-import-type EquationJson from \Notion\Common\Equation
 *
 * @psalm-type EquationBlockJson = array{
 *      equation: EquationJson,
 * }
 *
 * @psalm-immutable
 */
class EquationBlock implements BlockInterface
{
    private const TYPE = Block::TYPE_EQUATION;

    private Block $block;

    private Equation $equation;

    private function __construct(Block $block, Equation $equation)
    {
        if (!$block->isEquation()) {
            throw new BlockTypeException(self::TYPE);
        }

        $this->block = $block;
        $this->equation = $equation;
    }

    public static function create(string $expression = ""): self
    {
        $block = Block::create(self::TYPE);
        $equation = Equation::create($expression);

        return new self($block, $equation);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockJson $array */
        $block = Block::fromArray($array);

        /** @psalm-var EquationBlockJson $array */
        $equation = Equation::fromArray($array[self::TYPE]);

        return new self($block, $equation);
    }

    public function toArray(): array
    {
        $array = $this->block->toArray();

        $array[self::TYPE] = $this->equation->toArray();

        return $array;
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            self::TYPE => $this->equation->toArray(),
            "archived" => $this->block()->archived(),
        ];
    }

    public function block(): Block
    {
        return $this->block;
    }

    public function equation(): Equation
    {
        return $this->equation;
    }

    public function withEquation(Equation $equation): self
    {
        return new self($this->block, $equation);
    }

    public function changeChildren(array $children): self
    {
        throw new NotionException(
            "This block does not support children.",
            "no_children_support",
        );
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->block->archive(),
            $this->equation,
        );
    }
}
