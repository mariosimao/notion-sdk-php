<?php

namespace Notion\Test\Unit\DataSources\Properties;

use Notion\DataSources\Properties\PropertyFactory;
use Notion\DataSources\Properties\Relation;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase
{
    public function test_create_unidirectional(): void
    {
        $dataSourceId = "04eecdf8-f2d9-43a0-abbc-476182192c8f";
        $relation = Relation::createUnidirectional("My relation", $dataSourceId);

        $this->assertSame("My relation", $relation->metadata()->name);
        $this->assertSame($dataSourceId, $relation->dataSourceId);
        $this->assertTrue($relation->isUnidirectional());
        $this->assertFalse($relation->isBidirectional());
    }

    public function test_create_bidirectional(): void
    {
        $dataSourceId = "04eecdf8-f2d9-43a0-abbc-476182192c8f";
        $syncedPropertyName = "Prop name";
        $syncedPropertyId = "12ac";

        $relation = Relation::createBidirectional(
            "My relation",
            $dataSourceId,
            $syncedPropertyName,
            $syncedPropertyId,
        );

        $this->assertSame("My relation", $relation->metadata()->name);
        $this->assertSame($dataSourceId, $relation->dataSourceId);
        $this->assertSame($syncedPropertyName, $relation->syncedPropertyName);
        $this->assertSame($syncedPropertyId, $relation->syncedPropertyId);
        $this->assertTrue($relation->isBidirectional());
        $this->assertFalse($relation->isUnidirectional());
    }

    public function test_change_to_unidirectional(): void
    {
        $dataSourceId = "04eecdf8-f2d9-43a0-abbc-476182192c8f";
        $syncedPropertyName = "Prop name";
        $syncedPropertyId = "12ac";

        $relation = Relation::createBidirectional(
            "My relation",
            $dataSourceId,
            $syncedPropertyName,
            $syncedPropertyId,
        );

        $relation = $relation->changeToUnidirectional();

        $this->assertTrue($relation->isUnidirectional());
        $this->assertSame($dataSourceId, $relation->dataSourceId);
        $this->assertNull($relation->syncedPropertyId);
        $this->assertNull($relation->syncedPropertyName);
    }

    public function test_change_to_bidirectional(): void
    {
        $dataSourceId = "04eecdf8-f2d9-43a0-abbc-476182192c8f";
        $relation = Relation::createUnidirectional("My relation", $dataSourceId);

        $syncedPropertyName = "Prop name";
        $syncedPropertyId = "12ac";

        $relation = $relation->changeToBidirectional($syncedPropertyName, $syncedPropertyId);

        $this->assertTrue($relation->isBidirectional());
        $this->assertSame($dataSourceId, $relation->dataSourceId);
        $this->assertSame($syncedPropertyName, $relation->syncedPropertyName);
        $this->assertSame($syncedPropertyId, $relation->syncedPropertyId);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "relation",
            "relation" => [
                "data_source_id" => "84660ad0-9cb9-45d0-aae0-91e2c2526e12",
                "type" => "single_property",
                "single_property" => new \stdClass(),
            ],
        ];
        $relation = Relation::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertSame("84660ad0-9cb9-45d0-aae0-91e2c2526e12", $relation->dataSourceId);
        $this->assertEquals($array, $relation->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
