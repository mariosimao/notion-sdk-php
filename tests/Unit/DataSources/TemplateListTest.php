<?php

namespace Notion\Test\Unit\DataSources;

use Notion\DataSources\TemplateList;
use PHPUnit\Framework\TestCase;

class TemplateListTest extends TestCase
{
    public function test_from_array(): void
    {
        $list = TemplateList::fromArray([
            "templates" => [
                [ "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e", "name" => "Bug", "is_default" => false ],
                [ "id" => "b7e80c0b-a766-43c3-a9e9-21ce94595e0e", "name" => "Feature", "is_default" => true ],
            ],
            "has_more" => true,
            "next_cursor" => "c7e80c0b-a766-43c3-a9e9-21ce94595e0e",
        ]);

        $this->assertCount(2, $list->templates);
        $this->assertSame("Bug", $list->templates[0]->name);
        $this->assertFalse($list->templates[0]->isDefault);
        $this->assertSame("a7e80c0b-a766-43c3-a9e9-21ce94595e0e", $list->templates[0]->id);
        $this->assertTrue($list->hasMore);
        $this->assertSame("c7e80c0b-a766-43c3-a9e9-21ce94595e0e", $list->nextCursor);
        $this->assertSame("Feature", $list->defaultTemplate()?->name);
    }

    public function test_empty_list(): void
    {
        $list = TemplateList::fromArray([
            "templates" => [],
            "has_more" => false,
            "next_cursor" => null,
        ]);

        $this->assertEmpty($list->templates);
        $this->assertFalse($list->hasMore);
        $this->assertNull($list->nextCursor);
        $this->assertNull($list->defaultTemplate());
    }

    public function test_template_to_array(): void
    {
        $array = [ "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e", "name" => "Bug", "is_default" => true ];
        $list = TemplateList::fromArray([ "templates" => [ $array ], "has_more" => false, "next_cursor" => null ]);

        $this->assertSame($array, $list->templates[0]->toArray());
    }
}
