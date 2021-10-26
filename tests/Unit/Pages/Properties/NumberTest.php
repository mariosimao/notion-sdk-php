<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Common\RichText;
use Notion\Pages\Properties\Number;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    public function test_create(): void
    {
        $text = Number::create(123);

        $this->assertEquals(123, $text->number());
        $this->assertEquals("", $text->property()->id());
        $this->assertEquals("number", $text->property()->type());
        $this->assertTrue($text->property()->isNumber());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id"    => "a7ede3b7-c7ae-4eb8-b415-a7f80ac4dfe5",
            "type"  => "number",
            "number" => 123,
        ];

        $text = Number::fromArray($array);
        $this->assertEquals($array, $text->toArray());
    }

    public function test_change_value(): void
    {
        $text = Number::create(123)->withNumber(0.25);
        $this->assertEquals(0.25, $text->number());
    }
}
