<?php

namespace Notion\Common;

/**
 * @psalm-type EquationJson = array{ expression: string }
 *
 * @psalm-immutable
 */
class Equation
{
    private function __construct(
        public readonly string $expression
    ) {
    }

    public static function fromString(string $expression): self
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
}
