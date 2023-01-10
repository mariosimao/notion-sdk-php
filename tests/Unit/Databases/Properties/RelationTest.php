<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\Relation;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase
{
    public function test_create_unidirectional(): void
    {
        $databaseId = "04eecdf8-f2d9-43a0-abbc-476182192c8f";
        $relation = Relation::createUnidirectional("My relation", $databaseId);

        $this->assertSame("My relation", $relation->metadata()->name);
        $this->assertSame($databaseId, $relation->databaseId);
        $this->assertTrue($relation->isUniderectional());
    }

    public function test_create_bidirectional(): void
    {
        $databaseId = "04eecdf8-f2d9-43a0-abbc-476182192c8f";
        $syncedPropertyName = "Prop name";
        $syncedPropertyId = "12ac";

        $relation = Relation::createBidirectional(
            "My relation",
            $databaseId,
            $syncedPropertyName,
            $syncedPropertyId,
        );

        $this->assertSame("My relation", $relation->metadata()->name);
        $this->assertSame($databaseId, $relation->databaseId);
        $this->assertSame($syncedPropertyName, $relation->syncedPropertyName);
        $this->assertSame($syncedPropertyId, $relation->syncedPropertyId);
        $this->assertTrue($relation->isBiderectional());
    }

    public function test_change_to_unidirectional(): void
    {
        $databaseId = "04eecdf8-f2d9-43a0-abbc-476182192c8f";
        $syncedPropertyName = "Prop name";
        $syncedPropertyId = "12ac";

        $relation = Relation::createBidirectional(
            "My relation",
            $databaseId,
            $syncedPropertyName,
            $syncedPropertyId,
        );

        $relation = $relation->changeToUnidirectional();

        $this->assertTrue($relation->isUniderectional());
        $this->assertNull($relation->syncedPropertyId);
        $this->assertNull($relation->syncedPropertyName);
    }

    public function test_change_to_bidirectional(): void
    {
        $databaseId = "04eecdf8-f2d9-43a0-abbc-476182192c8f";
        $relation = Relation::createUnidirectional("My relation", $databaseId);

        $syncedPropertyName = "Prop name";
        $syncedPropertyId = "12ac";


        $relation = $relation->changeToBidirectional($syncedPropertyName, $syncedPropertyId);

        $this->assertTrue($relation->isBiderectional());
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
                "database_id" => "84660ad0-9cb9-45d0-aae0-91e2c2526e12",
                "type" => "single_property",
                "single_property" => new \stdClass(),
            ],
        ];
        $relation = Relation::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $relation->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
