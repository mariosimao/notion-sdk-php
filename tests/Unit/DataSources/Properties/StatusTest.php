<?php

namespace Notion\Test\Unit\DataSources\Properties;

use Notion\Common\Color;
use Notion\DataSources\Properties\PropertyFactory;
use Notion\DataSources\Properties\PropertyType;
use Notion\DataSources\Properties\Status;
use Notion\DataSources\Properties\StatusGroupName;
use Notion\DataSources\Properties\StatusOption;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "Status",
            "type"  => "status",
            "status" => [
                "groups" => [
                    [ "id" => "111", "option_ids" => ["aaa"], "color" => "green", "name" => "To-do" ],
                    [ "id" => "222", "option_ids" => ["bbb"], "color" => "yellow", "name" => "In Progress" ],
                    [ "id" => "333", "option_ids" => ["ccc"], "color" => "red", "name" => "Complete" ],
                ],
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default" ],
                    [ "id" => "bbb", "name" => "Option B", "color" => "default" ],
                    [ "id" => "ccc", "name" => "Option C", "color" => "default" ],
                ],
            ],
        ];
        $status = Status::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $status->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
        $this->assertSame(PropertyType::Status, $status->metadata()->type);
    }

    public function test_array_conversion_with_default_option(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "Status",
            "type"  => "status",
            "status" => [
                "default_option" => "Option A",
                "groups" => [
                    [ "id" => "111", "option_ids" => ["aaa"], "color" => "gray", "name" => "To-do" ],
                ],
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default", "description" => "First" ],
                ],
            ],
        ];
        $status = Status::fromArray($array);

        $this->assertSame("Option A", $status->defaultOption);
        $this->assertSame("First", $status->options[0]->description);
        $this->assertEquals($array, $status->toArray());
    }

    public function test_create(): void
    {
        $status = Status::create("Progress");

        $this->assertSame("Progress", $status->metadata()->name);
        $this->assertSame(PropertyType::Status, $status->metadata()->type);
        $this->assertSame([], $status->options);
        $this->assertSame([], $status->groups);
        $this->assertNull($status->defaultOption);
    }

    public function test_create_without_options_request_uses_default_options(): void
    {
        $status = Status::create();

        $this->assertEquals(
            [
                "id" => "",
                "name" => "Status",
                "type" => "status",
                "status" => new \stdClass(),
            ],
            $status->toRequestArray(),
        );
    }

    public function test_create_with_custom_options(): void
    {
        $status = Status::create("Status", [
            StatusOption::fromName("Backlog")->changeGroup(StatusGroupName::ToDo),
            StatusOption::fromName("Doing")
                ->changeColor(Color::Blue)
                ->changeGroup(StatusGroupName::InProgress),
        ])->addOption(
            StatusOption::fromName("Shipped")
                ->changeColor(Color::Green)
                ->changeDescription("Released to users")
                ->changeGroup(StatusGroupName::Complete),
        );

        $this->assertEquals(
            [
                "options" => [
                    [ "name" => "Backlog", "group" => "To-do" ],
                    [ "name" => "Doing", "color" => "blue", "group" => "In progress" ],
                    [
                        "name" => "Shipped",
                        "color" => "green",
                        "description" => "Released to users",
                        "group" => "Complete",
                    ],
                ],
            ],
            $status->toRequestArray()["status"],
        );
    }

    public function test_update_existing_options(): void
    {
        $status = Status::fromArray([
            "id"    => "abc",
            "name"  => "Status",
            "type"  => "status",
            "status" => [
                "default_option" => "Option A",
                "groups" => [
                    [ "id" => "111", "option_ids" => ["aaa"], "color" => "gray", "name" => "To-do" ],
                    [ "id" => "222", "option_ids" => [], "color" => "blue", "name" => "In progress" ],
                ],
                "options" => [
                    [ "id" => "aaa", "name" => "Option A", "color" => "default" ],
                    [ "id" => "bbb", "name" => "Option B", "color" => "default" ],
                ],
            ],
        ]);

        $optionA = $status->options[0]
            ->changeColor(Color::Red)
            ->changeGroup("In progress");
        $status = $status->changeOptions($optionA, StatusOption::fromName("Option C"));

        $request = $status->toRequestArray();

        $this->assertEquals(
            [
                "id" => "abc",
                "name" => "Status",
                "type" => "status",
                "status" => [
                    "options" => [
                        [ "id" => "aaa", "color" => "red", "group" => "In progress" ],
                        [ "name" => "Option C" ],
                    ],
                ],
            ],
            $request,
        );
        $this->assertEquals($request, PropertyFactory::toRequestArray($status));
        $this->assertSame("Option A", $status->defaultOption);
        $this->assertCount(2, $status->groups);
    }
}
