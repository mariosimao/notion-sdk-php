<?php

namespace Notion\Common;

/**
 * @psalm-type EquationJson = array{ expression: string }
 *
 * @psalm-immutable
 */
class Equation
{
    private string $expression;

    private function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    public static function create(string $expression): self
    {
        return new self($expression);
    }

    /**
     * @param EquationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["expression"]);
    }

    public function toArray(): array
    {
        return [
            "expression" => $this->expression,
        ];
    }

    public function expression(): string
    {
        return $this->expression;
    }

    public function withExpression(string $expression): self
    {
        return new self($expression);
    }
}
