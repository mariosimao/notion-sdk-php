<?php

namespace Notion\Test\Unit\Databases\Properties;

use Notion\Common\RichText;
use Notion\Databases\Properties\PropertyFactory;
use Notion\Databases\Properties\PropertyType;
use Notion\Databases\Properties\RichTextProperty;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public function test_create(): void
    {
        $text = RichTextProperty::create("Dummy prop name");

        $this->assertEquals("Dummy prop name", $text->metadata()->name);
        $this->assertEquals(PropertyType::RichText, $text->metadata()->type);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "abc",
            "name"  => "dummy",
            "type"  => "rich_text",
            "rich_text" => new \stdClass(),
        ];
        $text = RichTextProperty::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $text->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_new_line(): void
    {
        $text = RichText::newLine();

        $this->assertSame("\n", $text->toString());
    }

    public function test_mutiple_to_string(): void
    {
        $text = [
            RichText::fromString("Multiple ")->bold(),
            RichText::fromString("text ")->italic(),
            RichText::fromString("example")->underline(),
        ];

        $this->assertSame("Multiple text example", RichText::multipleToString(...$text));
    }
}
