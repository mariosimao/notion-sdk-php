<?php

namespace Notion\Test\Unit\DataSources\Properties;

use Notion\DataSources\Properties\PropertyFactory;
use Notion\DataSources\Properties\PropertyType;
use Notion\DataSources\Properties\Rollup;
use Notion\DataSources\Properties\RollupFunction;
use PHPUnit\Framework\TestCase;

class RollupTest extends TestCase
{
    public function test_create(): void
    {
        $rollup = Rollup::create(
            "Estimated total time",
            "Tasks",
            "Days to complete",
        );

        $this->assertSame("Estimated total time", $rollup->metadata()->name);
        $this->assertSame(PropertyType::Rollup, $rollup->metadata()->type);
        $this->assertSame(RollupFunction::ShowOriginal, $rollup->function);
        $this->assertSame("Tasks", $rollup->relationPropertyName);
        $this->assertSame("Days to complete", $rollup->rollupPropertyName);
        $this->assertNull($rollup->relationPropertyId);
        $this->assertNull($rollup->rollupPropertyId);
    }

    public function test_create_with_function(): void
    {
        $rollup = Rollup::create(
            "Total time",
            "Tasks",
            "Days to complete",
            RollupFunction::Sum,
        );

        $this->assertSame(RollupFunction::Sum, $rollup->function);
    }

    public function test_create_by_id(): void
    {
        $rollup = Rollup::createById(
            "Total time",
            "rel_123",
            "prop_456",
            RollupFunction::Count,
        );

        $this->assertSame("Total time", $rollup->metadata()->name);
        $this->assertSame(PropertyType::Rollup, $rollup->metadata()->type);
        $this->assertSame(RollupFunction::Count, $rollup->function);
        $this->assertSame("rel_123", $rollup->relationPropertyId);
        $this->assertSame("prop_456", $rollup->rollupPropertyId);
        $this->assertNull($rollup->relationPropertyName);
        $this->assertNull($rollup->rollupPropertyName);
    }

    public function test_change_function(): void
    {
        $rollup = Rollup::create("Total time", "Tasks", "Days")
            ->changeFunction(RollupFunction::Average);

        $this->assertSame(RollupFunction::Average, $rollup->function);
    }

    public function test_change_relation_property_name(): void
    {
        $rollup = Rollup::create("Total time", "Tasks", "Days")
            ->changeRelationPropertyName("Subtasks");

        $this->assertSame("Subtasks", $rollup->relationPropertyName);
    }

    public function test_change_relation_property_id(): void
    {
        $rollup = Rollup::create("Total time", "Tasks", "Days")
            ->changeRelationPropertyId("rel_999");

        $this->assertSame("rel_999", $rollup->relationPropertyId);
    }

    public function test_change_rollup_property_name(): void
    {
        $rollup = Rollup::create("Total time", "Tasks", "Days")
            ->changeRollupPropertyName("Hours");

        $this->assertSame("Hours", $rollup->rollupPropertyName);
    }

    public function test_change_rollup_property_id(): void
    {
        $rollup = Rollup::create("Total time", "Tasks", "Days")
            ->changeRollupPropertyId("prop_999");

        $this->assertSame("prop_999", $rollup->rollupPropertyId);
    }

    public function test_target_property_aliases(): void
    {
        $rollup = Rollup::create("Total time", "Tasks", "Days")
            ->changeTargetPropertyName("Hours")
            ->changeTargetPropertyId("prop_123");

        $this->assertSame("Hours", $rollup->targetPropertyName());
        $this->assertSame("prop_123", $rollup->targetPropertyId());
        $this->assertSame("Hours", $rollup->rollupPropertyName);
        $this->assertSame("prop_123", $rollup->rollupPropertyId);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "%5E%7Cy%3C",
            "name"  => "Estimated total project time",
            "type"  => "rollup",
            "rollup" => [
                "function"               => "sum",
                "relation_property_name" => "Tasks",
                "relation_property_id"   => "Y]<y",
                "rollup_property_name"   => "Days to complete",
                "rollup_property_id"     => "\\nyY",
            ],
        ];

        $rollup = Rollup::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertInstanceOf(Rollup::class, $fromFactory);
        $this->assertSame("Estimated total project time", $rollup->metadata()->name);
        $this->assertSame(RollupFunction::Sum, $rollup->function);
        $this->assertSame("Tasks", $rollup->relationPropertyName);
        $this->assertSame("Y]<y", $rollup->relationPropertyId);
        $this->assertSame("Days to complete", $rollup->rollupPropertyName);
        $this->assertSame("\\nyY", $rollup->rollupPropertyId);

        $this->assertEquals($array, $rollup->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_array_conversion_names_only(): void
    {
        $rollup = Rollup::create("Total tasks", "Tasks", "Name", RollupFunction::Count);

        $expected = [
            "id"   => "",
            "name" => "Total tasks",
            "type" => "rollup",
            "rollup" => [
                "function"               => "count",
                "relation_property_name" => "Tasks",
                "rollup_property_name"   => "Name",
            ],
        ];

        $this->assertEquals($expected, $rollup->toArray());
    }

    public function test_array_conversion_ids_only(): void
    {
        $rollup = Rollup::createById("Total tasks", "rel_123", "prop_456", RollupFunction::Count);

        $expected = [
            "id"   => "",
            "name" => "Total tasks",
            "type" => "rollup",
            "rollup" => [
                "function"             => "count",
                "relation_property_id" => "rel_123",
                "rollup_property_id"   => "prop_456",
            ],
        ];

        $this->assertEquals($expected, $rollup->toArray());
    }
}
