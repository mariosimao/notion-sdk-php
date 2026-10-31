<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\NativeIcon;
use Notion\Common\NativeIconColor;
use PHPUnit\Framework\TestCase;

class NativeIconTest extends TestCase
{
    public function test_create_defaults_to_gray(): void
    {
        $icon = NativeIcon::create("pizza");

        $this->assertSame("pizza", $icon->name);
        $this->assertSame(NativeIconColor::Gray, $icon->color);
    }

    public function test_create_with_color(): void
    {
        $icon = NativeIcon::create("pizza", NativeIconColor::Blue);

        $this->assertSame(NativeIconColor::Blue, $icon->color);
    }

    public function test_array_conversion(): void
    {
        $array = [
            "type" => "icon",
            "icon" => [ "name" => "pizza", "color" => "lightgray" ],
        ];

        $icon = NativeIcon::fromArray($array);

        $this->assertSame("pizza", $icon->name);
        $this->assertSame(NativeIconColor::LightGray, $icon->color);
        $this->assertSame($array, $icon->toArray());
    }

    public function test_from_array_without_color_defaults_to_gray(): void
    {
        $icon = NativeIcon::fromArray([ "type" => "icon", "icon" => [ "name" => "pizza" ] ]);

        $this->assertSame(NativeIconColor::Gray, $icon->color);
    }

    public function test_change_name(): void
    {
        $icon = NativeIcon::create("pizza", NativeIconColor::Red)->changeName("star");

        $this->assertSame("star", $icon->name);
        $this->assertSame(NativeIconColor::Red, $icon->color);
    }

    public function test_change_color(): void
    {
        $icon = NativeIcon::create("pizza")->changeColor(NativeIconColor::Purple);

        $this->assertSame("pizza", $icon->name);
        $this->assertSame(NativeIconColor::Purple, $icon->color);
    }
}
