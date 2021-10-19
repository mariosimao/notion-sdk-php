<?php

namespace Notion\Common;

/**
 * @psalm-type EquationJson = array{ expression: string }
 */
class Equation
{
    private string $expression;

    private function __construct(string $expression)
    {
        $this->expression = $expression;
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
}
