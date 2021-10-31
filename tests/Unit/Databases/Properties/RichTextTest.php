<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Databases\Properties\Factory;
use Notion\Databases\Properties\RichText;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public function test_create(): void
    {
        $text = RichText::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $text->property()->name());
        $this->assertTrue($text->property()->isRichText());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "rich_text",
            "rich_text" => [],
        ];
        $text = RichText::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $text->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
