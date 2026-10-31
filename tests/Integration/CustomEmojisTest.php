<?php

namespace Notion\Test\Integration;

use Notion\CustomEmojis\CustomEmoji;
use Notion\CustomEmojis\Query;
use PHPUnit\Framework\TestCase;

class CustomEmojisTest extends TestCase
{
    public function test_list_custom_emojis(): void
    {
        $client = Helper::client();

        $result = $client->customEmojis()->list(Query::all()->changePageSize(1));

        $this->assertLessThanOrEqual(1, count($result->results));
        $this->assertContainsOnlyInstancesOf(CustomEmoji::class, $result->results);
        if ($result->hasMore) {
            $this->assertNotNull($result->nextCursor);
        }
    }

    public function test_find_all_custom_emojis(): void
    {
        $client = Helper::client();

        $emojis = $client->customEmojis()->findAll();

        $this->assertContainsOnlyInstancesOf(CustomEmoji::class, $emojis);
    }

    public function test_find_custom_emoji_by_name(): void
    {
        $client = Helper::client();

        $emojis = $client->customEmojis()->findAll();
        if ($emojis === []) {
            $this->assertNull($client->customEmojis()->findByName("inexistent-emoji-name"));
            return;
        }

        $emoji = $client->customEmojis()->findByName($emojis[0]->name);
        $this->assertEquals($emojis[0], $emoji);

        $filtered = $client->customEmojis()->findAll($emojis[0]->name);
        $this->assertContainsEquals($emojis[0], $filtered);
        foreach ($filtered as $e) {
            $this->assertSame($emojis[0]->name, $e->name);
        }
    }
}
