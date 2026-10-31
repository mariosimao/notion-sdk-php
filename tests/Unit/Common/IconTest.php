<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
use Notion\Common\NativeIcon;
use Notion\Common\NativeIconColor;
use Notion\Exceptions\IconException;
use PHPUnit\Framework\TestCase;

class IconTest extends TestCase
{
    public function test_icon_from_file_array_conversion(): void
    {
        $file = File::createExternal("http://example.com/icon.png");
        $icon = Icon::fromFile($file);

        $expected = [
            "type" => "external",
            "external" => ["url" => "http://example.com/icon.png"],
        ];

        $this->assertEquals($expected, $icon->toArray());
    }

    public function test_icon_from_native_icon_array_conversion(): void
    {
        $icon = Icon::fromNativeIcon(NativeIcon::create("pizza", NativeIconColor::Blue));

        $expected = [
            "type" => "icon",
            "icon" => ["name" => "pizza", "color" => "blue"],
        ];

        $this->assertTrue($icon->isNativeIcon());
        $this->assertFalse($icon->isEmoji());
        $this->assertFalse($icon->isFile());
        $this->assertEquals($expected, $icon->toArray());
    }

    public function test_from(): void
    {
        $this->assertTrue(Icon::from(Emoji::fromString("⭐"))->isEmoji());
        $this->assertTrue(Icon::from(File::createExternal("http://example.com/icon.png"))->isFile());
        $this->assertTrue(Icon::from(NativeIcon::create("pizza"))->isNativeIcon());

        $icon = Icon::fromEmoji(Emoji::fromString("⭐"));
        $this->assertSame($icon, Icon::from($icon));
    }

    public function test_from_array(): void
    {
        $emoji = ["type" => "emoji", "emoji" => "⭐"];
        $external = ["type" => "external", "external" => ["url" => "http://example.com/icon.png"]];
        $native = ["type" => "icon", "icon" => ["name" => "pizza", "color" => "blue"]];

        $this->assertEquals($emoji, Icon::fromArray($emoji)->toArray());
        $this->assertEquals($external, Icon::fromArray($external)->toArray());
        $this->assertEquals($native, Icon::fromArray($native)->toArray());
    }

    public function test_from_array_unsupported_type(): void
    {
        $this->expectException(IconException::class);

        Icon::fromArray(["type" => "unsupported"]);
    }

    public function test_try_from_array_unsupported_type(): void
    {
        $this->assertNull(Icon::tryFromArray(["type" => "unsupported"]));
    }
}
