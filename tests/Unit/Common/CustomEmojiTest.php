<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\CustomEmoji;
use PHPUnit\Framework\TestCase;

class CustomEmojiTest extends TestCase
{
    public function test_create_from_id(): void
    {
        $customEmoji = CustomEmoji::fromId("45ce454c-d427-4f53-9489-e5d0f3d1db6b");

        $this->assertEquals("45ce454c-d427-4f53-9489-e5d0f3d1db6b", $customEmoji->id);
        $this->assertNull($customEmoji->name);
        $this->assertNull($customEmoji->url);
    }

    public function test_from_array(): void
    {
        $customEmoji = CustomEmoji::fromArray([
            "type" => "custom_emoji",
            "custom_emoji" => [
                "id" => "45ce454c-d427-4f53-9489-e5d0f3d1db6b",
                "name" => "bufo",
                "url" => "https://example.com/bufo.png",
            ],
        ]);

        $this->assertEquals("45ce454c-d427-4f53-9489-e5d0f3d1db6b", $customEmoji->id);
        $this->assertEquals("bufo", $customEmoji->name);
        $this->assertEquals("https://example.com/bufo.png", $customEmoji->url);
    }

    public function test_to_array_only_sends_id(): void
    {
        $customEmoji = CustomEmoji::fromArray([
            "type" => "custom_emoji",
            "custom_emoji" => [
                "id" => "45ce454c-d427-4f53-9489-e5d0f3d1db6b",
                "name" => "bufo",
                "url" => "https://example.com/bufo.png",
            ],
        ]);

        $expected = [
            "type" => "custom_emoji",
            "custom_emoji" => [ "id" => "45ce454c-d427-4f53-9489-e5d0f3d1db6b" ],
        ];

        $this->assertEquals($expected, $customEmoji->toArray());
    }
}
