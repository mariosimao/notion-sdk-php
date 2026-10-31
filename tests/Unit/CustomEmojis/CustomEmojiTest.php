<?php

namespace Notion\Test\Unit\CustomEmojis;

use Notion\CustomEmojis\CustomEmoji;
use PHPUnit\Framework\TestCase;

class CustomEmojiTest extends TestCase
{
    public function test_array_conversion(): void
    {
        $array = [
            "id"   => "45ce454c-d427-4f53-9489-e5d0f3d1db6b",
            "name" => "party-parrot",
            "url"  => "https://example.com/party-parrot.gif",
        ];

        $emoji = CustomEmoji::fromArray($array);

        $this->assertSame("45ce454c-d427-4f53-9489-e5d0f3d1db6b", $emoji->id);
        $this->assertSame("party-parrot", $emoji->name);
        $this->assertSame("https://example.com/party-parrot.gif", $emoji->url);
        $this->assertSame($array, $emoji->toArray());
    }
}
