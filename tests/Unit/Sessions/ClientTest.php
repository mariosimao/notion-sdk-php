<?php

namespace Notion\Test\Unit\Sessions;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\Exceptions\ApiException;
use Notion\Notion;
use Notion\Sessions\Client as SessionsClient;
use Notion\Sessions\Message;
use Notion\Sessions\StreamEventType;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/** Streaming can not be covered by integration tests deterministically. */
final class ClientTest extends TestCase
{
    private RequestInterface|null $request = null;

    public function test_stream(): void
    {
        $body = "event: session.snapshot\n"
            . "data: {\"type\":\"session.snapshot\",\"session\":{\"object\":\"session\",\"id\":\"s\","
            . "\"agent_id\":\"a\",\"title\":\"\",\"status\":\"queued\",\"created_at\":\"2026-08-14T12:10:00.000Z\","
            . "\"updated_at\":\"2026-08-14T12:10:00.000Z\"}}\n\n"
            . ": keep-alive\n\n"
            . "data: not json\n\n"
            . "event: stream.end\n"
            . "data: {\"session_id\":\"s\",\"status\":\"completed\"}\n\n";

        $notion = $this->notion([ new Response(200, [ "Content-Type" => "text/event-stream" ], $body) ]);

        $events = iterator_to_array($notion->sessions()->stream(Message::start("a", "Hi")), false);

        $this->assertCount(2, $events);
        $this->assertSame(StreamEventType::SessionSnapshot, $events[0]->type);
        $this->assertSame(StreamEventType::StreamEnd, $events[1]->type);
        $this->assertSame("s", $events[1]->sessionId);

        $request = $this->request;
        $this->assertNotNull($request);
        $this->assertSame("POST", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/sessions", (string) $request->getUri());
        $this->assertSame("text/event-stream", $request->getHeaderLine("Accept"));
        $this->assertSame(SessionsClient::MIN_API_VERSION, $request->getHeaderLine("Notion-Version"));
        $this->assertSame("{\"message\":\"Hi\",\"agent_id\":\"a\"}", (string) $request->getBody());
    }

    public function test_resume_stream_error(): void
    {
        $notion = $this->notion([
            new Response(400, [], "{\"code\":\"validation_error\",\"message\":\"Bad cursor\"}"),
        ]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage("Bad cursor");

        $notion->sessions()->resumeStream("s", "event-id");
    }

    /** @param list<Response> $responses */
    private function notion(array $responses): Notion
    {
        $mock = new MockHandler($responses);
        $handler = function (RequestInterface $request, array $options) use ($mock): mixed {
            $this->request = $request;
            return $mock($request, $options);
        };

        return Notion::createWithPsrImplementations(
            new Client([ "handler" => $handler ]),
            new HttpFactory(),
            "secret",
        );
    }
}
