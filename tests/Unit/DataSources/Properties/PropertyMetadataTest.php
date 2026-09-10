<?php

namespace Notion\Test\Unit\DataSources\Properties;

use Notion\DataSources\Properties\PropertyMetadata;
use Notion\DataSources\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class PropertyMetadataTest extends TestCase
{
    public function test_create(): void
    {
        $metadata = PropertyMetadata::create("abc", "Dummy prop name", PropertyType::CreatedBy, "foo bar");

        $this->assertEquals("abc", $metadata->id);
        $this->assertEquals("Dummy prop name", $metadata->name);
        $this->assertEquals(PropertyType::CreatedBy, $metadata->type);
        $this->assertEquals("foo bar", $metadata->description);
    }

    public function test_from_array(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "created_by",
            "created_by" => new \stdClass(),
            "description" => "foo bar",
        ];
        $metadata = PropertyMetadata::fromArray($array);

        $this->assertEquals($array["id"], $metadata->id);
        $this->assertEquals($array["name"], $metadata->name);
        $this->assertEquals(PropertyType::CreatedBy, $metadata->type);
        $this->assertEquals($array["description"], $metadata->description);

        $toArray = $metadata->toArray();

        $this->assertEqualsCanonicalizing(['id', 'name', 'type', 'description'], array_keys($toArray));
    }

    public function test_from_array_without_description(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "created_by",
            "created_by" => new \stdClass(),
        ];

        $metadata = PropertyMetadata::fromArray($array);

        $this->assertEquals($array["id"], $metadata->id);
        $this->assertEquals($array["name"], $metadata->name);
        $this->assertEquals(PropertyType::CreatedBy, $metadata->type);
        $this->assertNull($metadata->description);

        $toArray = $metadata->toArray();

        // When there is no description, its key won't show up
        $this->assertEqualsCanonicalizing(['id', 'name', 'type'], array_keys($toArray));
    }
}
