<?php

namespace Notion\Test\Unit\CustomEmojis;

use Notion\CustomEmojis\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function test_from_array(): void
    {
        $result = Result::fromArray([
            "results" => [
                [
                    "id"   => "45ce454c-d427-4f53-9489-e5d0f3d1db6b",
                    "name" => "party-parrot",
                    "url"  => "https://example.com/party-parrot.gif",
                ],
            ],
            "next_cursor" => "b4b0ba7f-8ac5-4c1d-9a3b-d2a5e4a3d6c1",
            "has_more"    => true,
        ]);

        $this->assertCount(1, $result->results);
        $this->assertSame("party-parrot", $result->results[0]->name);
        $this->assertSame("b4b0ba7f-8ac5-4c1d-9a3b-d2a5e4a3d6c1", $result->nextCursor);
        $this->assertTrue($result->hasMore);
    }
}
