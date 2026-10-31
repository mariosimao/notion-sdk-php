<?php

namespace Notion\Test\Unit\DataSources\Properties;

use Notion\Common\Color;
use Notion\DataSources\Properties\StatusGroupName;
use Notion\DataSources\Properties\StatusOption;
use PHPUnit\Framework\TestCase;

class StatusOptionTest extends TestCase
{
    public function test_from_name(): void
    {
        $option = StatusOption::fromName("Shipped");

        $this->assertNull($option->id);
        $this->assertSame("Shipped", $option->name);
        $this->assertNull($option->color);
        $this->assertNull($option->description);
        $this->assertNull($option->group);
    }

    public function test_change_color(): void
    {
        $option = StatusOption::fromName("Shipped")->changeColor(Color::Green);

        $this->assertSame(Color::Green, $option->color);
    }

    public function test_change_name(): void
    {
        $option = StatusOption::fromId("abc")->changeName("Shipped");

        $this->assertSame("Shipped", $option->name);
    }

    public function test_change_description(): void
    {
        $option = StatusOption::fromName("Shipped")->changeDescription("Released to users");

        $this->assertSame("Released to users", $option->description);
    }

    public function test_change_group_with_enum(): void
    {
        $option = StatusOption::fromName("Shipped")->changeGroup(StatusGroupName::Complete);

        $this->assertSame("Complete", $option->group);
    }

    public function test_change_group_with_string(): void
    {
        $option = StatusOption::fromName("Shipped")->changeGroup("In progress");

        $this->assertSame("In progress", $option->group);
    }

    public function test_transformations_preserve_fields(): void
    {
        $option = StatusOption::fromId("abc")
            ->changeName("Shipped")
            ->changeColor(Color::Green)
            ->changeDescription("Released")
            ->changeGroup(StatusGroupName::Complete);

        $this->assertSame("abc", $option->id);
        $this->assertSame("Shipped", $option->name);
        $this->assertSame(Color::Green, $option->color);
        $this->assertSame("Released", $option->description);
        $this->assertSame("Complete", $option->group);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "name" => "Shipped",
            "color" => "green",
            "description" => "Released",
        ];

        $this->assertEquals($array, StatusOption::fromArray($array)->toArray());
    }

    public function test_null_description_from_array(): void
    {
        $option = StatusOption::fromArray([ "id" => "abc", "name" => "Shipped", "description" => null ]);

        $this->assertNull($option->description);
    }

    public function test_request_array_references_existing_option_by_id(): void
    {
        $option = StatusOption::fromArray([ "id" => "abc", "name" => "Shipped", "color" => "green" ])
            ->changeGroup(StatusGroupName::Complete);

        $this->assertEquals(
            [ "id" => "abc", "color" => "green", "group" => "Complete" ],
            $option->toRequestArray(),
        );
    }

    public function test_request_array_for_new_option(): void
    {
        $option = StatusOption::fromName("Shipped")->changeGroup(StatusGroupName::Complete);

        $this->assertEquals(
            [ "name" => "Shipped", "group" => "Complete" ],
            $option->toRequestArray(),
        );
    }
}
