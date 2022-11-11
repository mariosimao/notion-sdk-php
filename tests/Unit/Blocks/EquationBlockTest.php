<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\EquationBlock;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;
use Notion\Common\Equation;
use PHPUnit\Framework\TestCase;

class EquationBlockTest extends TestCase
{
    public function test_create_equation(): void
    {
        $equation = EquationBlock::fromString("a^2 + b^2 = c^2");

        $this->assertEquals("a^2 + b^2 = c^2", $equation->equation->expression);
    }

    public function test_create_from_array(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "equation",
            "equation"         => [ "expression" => "a^2 + b^2 = c^2" ],
        ];

        $equation = EquationBlock::fromArray($array);

        $this->assertEquals("a^2 + b^2 = c^2", $equation->equation->expression);

        $this->assertEquals($equation, BlockFactory::fromArray($array));
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockException::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "equation"         => [ "expression" => "a^2 + b^2 = c^2" ],
        ];

        EquationBlock::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $equation = EquationBlock::fromString("a^2 + b^2 = c^2");

        $expected = [
            "object"           => "block",
            "created_time"     => $equation->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $equation->metadata()->createdTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "equation",
            "equation"         => [ "expression" => "a^2 + b^2 = c^2" ],
        ];

        $this->assertEquals($expected, $equation->toArray());
    }

    public function test_replace_equation(): void
    {
        $equation = Equation::fromString("a^2 + b^2 = c^2");
        $equationBlock = EquationBlock::fromString()->changeEquation($equation);

        $this->assertEquals($equation, $equationBlock->equation);
    }

    public function test_no_children_support(): void
    {
        $block = EquationBlock::fromString();

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $block = EquationBlock::fromString();

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }
}
