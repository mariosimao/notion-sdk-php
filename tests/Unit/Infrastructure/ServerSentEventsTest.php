<?php

namespace Notion\Test\Unit\Infrastructure;

use GuzzleHttp\Psr7\Utils;
use Notion\Infrastructure\ServerSentEvents;
use PHPUnit\Framework\TestCase;

final class ServerSentEventsTest extends TestCase
{
    public function test_parse_events(): void
    {
        $body = ": comment\nevent: session.snapshot\nid: 1\ndata: {\"a\":1}\n\n"
            . "data: line1\ndata:line2\n\n"
            . "event: empty\n\n"
            . "data: last";

        $events = iterator_to_array(ServerSentEvents::parse(Utils::streamFor($body)), false);

        $this->assertCount(3, $events);
        $this->assertSame(["event" => "session.snapshot", "data" => "{\"a\":1}", "id" => "1"], $events[0]);
        $this->assertSame(["event" => null, "data" => "line1\nline2", "id" => null], $events[1]);
        $this->assertSame(["event" => null, "data" => "last", "id" => null], $events[2]);
    }

    public function test_parse_crlf_events(): void
    {
        $body = "data: a\r\n\r\ndata: b\r\rdata: c\r\n\r\n";

        $events = iterator_to_array(ServerSentEvents::parse(Utils::streamFor($body)), false);

        $this->assertSame(["a", "b", "c"], array_column($events, "data"));
    }

    public function test_parse_events_split_across_chunks(): void
    {
        $body = "data: " . str_repeat("x", 8190) . "\r\n\r\ndata: y\n\n";

        $events = iterator_to_array(ServerSentEvents::parse(Utils::streamFor($body)), false);

        $this->assertCount(2, $events);
        $this->assertSame(str_repeat("x", 8190), $events[0]["data"]);
        $this->assertSame("y", $events[1]["data"]);
    }
}
