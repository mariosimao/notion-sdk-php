<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use Notion\Pages\Properties\Relation;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase
{
    public function test_create(): void
    {
        $id1 = "5604389a-8de1-4ba6-a07f-ca346ff98f00";
        $id2 = "03d71291-5ca3-4daa-a1e8-f012d513e8c8";

        $relation = Relation::create($id1, $id2);

        $this->assertEquals([$id1, $id2], $relation->pageIds);
        $this->assertTrue($relation->metadata()->type === PropertyType::Relation);
    }

    public function test_replace_relation(): void
    {
        $id1 = "5604389a-8de1-4ba6-a07f-ca346ff98f00";
        $id2 = "03d71291-5ca3-4daa-a1e8-f012d513e8c8";

        $relation = Relation::create($id1)->changeRelations($id2);

        $this->assertEquals([$id2], $relation->pageIds);
    }

    public function test_add_relation(): void
    {
        $id1 = "5604389a-8de1-4ba6-a07f-ca346ff98f00";
        $id2 = "03d71291-5ca3-4daa-a1e8-f012d513e8c8";

        $relation = Relation::create($id1)->addRelation($id2);

        $this->assertEquals([$id1, $id2], $relation->pageIds);
    }

    public function test_remove_relation(): void
    {
        $id1 = "5604389a-8de1-4ba6-a07f-ca346ff98f00";
        $id2 = "03d71291-5ca3-4daa-a1e8-f012d513e8c8";

        $relation = Relation::create($id1, $id2);
        $relation = $relation->removeRelation($id2);

        $this->assertEquals([$id1], $relation->pageIds);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "relation",
            "relation" => [
                [ "id" => "264f3f43-3d87-4bb2-bb66-11812ab74eae" ],
                [ "id" => "f3902b7f-e9e2-4406-8c3f-5d07dbc87d66" ],
            ],
        ];

        $relation = Relation::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $relation->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
