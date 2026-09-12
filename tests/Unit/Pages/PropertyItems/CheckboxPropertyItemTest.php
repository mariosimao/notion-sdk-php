<?php

namespace Notion\Test\Unit\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;
use Notion\Pages\PropertyItems\CheckboxPropertyItem;
use PHPUnit\Framework\TestCase;

class CheckboxPropertyItemTest extends TestCase
{
    public function test_create(): void
    {
        $item = CheckboxPropertyItem::create(true, "chk-id");

        $this->assertSame("chk-id", $item->metadata()->id);
        $this->assertSame(PropertyType::Checkbox, $item->metadata()->type);
        $this->assertTrue($item->isChecked());
    }

    public function test_from_array(): void
    {
        $array = [
            "object" => "property_item",
            "id" => "KpQq",
            "type" => "checkbox",
            "checkbox" => true,
        ];

        $item = CheckboxPropertyItem::fromArray($array);

        $this->assertSame("KpQq", $item->metadata()->id);
        $this->assertTrue($item->checkbox);
        $this->assertSame($array, $item->toArray());
    }
}
