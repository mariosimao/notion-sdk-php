<?php

namespace Notion\Blocks;

use Notion\Common\Equation;

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
            throw new \Exception("Block must be of type " . self::TYPE);
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
}
