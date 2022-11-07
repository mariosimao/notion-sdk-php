<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Common\Color;
use Notion\Databases\Properties\SelectOption;
use PHPUnit\Framework\TestCase;

class SelectOptionTest extends TestCase
{
    public function test_change_color(): void
    {
        $option = SelectOption::fromName("Comedy")
            ->changeColor(Color::Red);

        $this->assertSame(Color::Red, $option->color);
    }

    public function test_change_name(): void
    {
        $option = SelectOption::fromId("abc123")
            ->changeName("Comedy");

        $this->assertSame("Comedy", $option->name);
    }
}
