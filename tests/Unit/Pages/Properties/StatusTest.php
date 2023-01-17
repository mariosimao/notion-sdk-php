<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Common\Color;
use Notion\Databases\Properties\StatusOption;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function test_from_id(): void
    {
        $id = "507b5b2c-5dc8-438b-ac5b-51d0c781ba65";
        $status = Status::fromId($id);

        $this->assertSame($id, $status->option->id);
    }

    public function test_from_name(): void
    {
        $name = "Done";
        $status = Status::fromName($name);

        $this->assertSame($name, $status->option->name);
    }

    public function test_from_option(): void
    {
        $option = StatusOption::fromName("Done");
        $status = Status::fromOption($option);

        $this->assertSame($option, $status->option);
    }

    public function test_change_color(): void
    {
        $color = Color::Green;

        $status = Status::fromId("5fdb657e-27d1-4832-842e-32231952a560")
            ->changeColor($color);

        $this->assertSame($color, $status->option->color);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"     => "ec27421d-cd03-4843-b8e9-ea08702d54ac",
            "type"   => "status",
            "status" => [
                "id"    => "032b00eb-228c-4ee3-ba1d-fb6e8a42cc95",
                "name"  => "Done",
                "color" => "default"
            ],
        ];

        $status = Status::fromArray($array);

        $this->assertEquals($array, $status->toArray());
        $this->assertSame(PropertyType::Status, $status->metadata()->type);
    }
}
