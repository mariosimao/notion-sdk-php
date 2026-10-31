<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\CustomEmoji;
use Notion\Common\Emoji;
use Notion\Common\File;
use Notion\Common\Icon;
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

    public function test_icon_from_custom_emoji(): void
    {
        $icon = Icon::fromCustomEmoji(CustomEmoji::fromId("45ce454c-d427-4f53-9489-e5d0f3d1db6b"));

        $this->assertTrue($icon->isCustomEmoji());
        $this->assertFalse($icon->isEmoji());
        $this->assertFalse($icon->isFile());
        $this->assertEquals(
            [
                "type" => "custom_emoji",
                "custom_emoji" => [ "id" => "45ce454c-d427-4f53-9489-e5d0f3d1db6b" ],
            ],
            $icon->toArray(),
        );
    }

    public function test_icon_from(): void
    {
        $this->assertTrue(Icon::from(Emoji::fromString("⭐"))->isEmoji());
        $this->assertTrue(Icon::from(CustomEmoji::fromId("45ce454c"))->isCustomEmoji());
        $this->assertTrue(Icon::from(File::createExternal("http://example.com/icon.png"))->isFile());

        $icon = Icon::fromEmoji(Emoji::fromString("⭐"));
        $this->assertSame($icon, Icon::from($icon));
    }

    public function test_icon_from_array(): void
    {
        $emoji = Icon::fromArray([ "type" => "emoji", "emoji" => "⭐" ]);
        $this->assertTrue($emoji->isEmoji());

        $customEmoji = Icon::fromArray([
            "type" => "custom_emoji",
            "custom_emoji" => [ "id" => "45ce454c-d427-4f53-9489-e5d0f3d1db6b", "name" => "bufo" ],
        ]);
        $this->assertTrue($customEmoji->isCustomEmoji());
        $this->assertEquals("bufo", $customEmoji->customEmoji?->name);

        $file = Icon::fromArray([
            "type" => "external",
            "external" => [ "url" => "http://example.com/icon.png" ],
        ]);
        $this->assertTrue($file->isFile());
    }

    public function test_try_from_array_unsupported_type(): void
    {
        $this->assertNull(Icon::tryFromArray([ "type" => "unknown" ]));
    }

    public function test_from_array_unsupported_type(): void
    {
        $this->expectException(IconException::class);

        /** @psalm-suppress InvalidArgument */
        Icon::fromArray([ "type" => "unknown" ]);
    }
}
