<?php

namespace Notion\Test\Unit\AsyncTasks;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Notion\AsyncTasks\AsyncTask;
use Notion\AsyncTasks\AsyncTaskStatus;
use Notion\Configuration;
use Notion\Exceptions\AsyncTaskException;
use Notion\Notion;
use Notion\Pages\Markdown\PageMarkdown;
use Notion\Pages\Markdown\ReplaceContent;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * The API only returns async tasks for large writes, so responses are mocked.
 */
class ClientTest extends TestCase
{
    private array $history = [];

    public function test_find(): void
    {
        $notion = $this->notion(self::json(AsyncTaskTest::taskArray("running", [ "poll_after_seconds" => 2 ])));

        $task = $notion->asyncTasks()->find("task_abc123");

        $this->assertSame(AsyncTaskStatus::Running, $task->status);
        $request = $this->request(0);
        $this->assertSame("GET", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/async_tasks/task_abc123", (string) $request->getUri());
    }

    public function test_wait_until_succeeded(): void
    {
        $notion = $this->notion(
            self::json(AsyncTaskTest::taskArray("running", [ "poll_after_seconds" => 0 ])),
            self::json(AsyncTaskTest::taskArray("succeeded", [ "result" => AsyncTaskTest::pageMarkdownArray() ])),
        );
        $task = AsyncTask::fromArray(AsyncTaskTest::taskArray("queued", [ "poll_after_seconds" => 0 ]));

        $task = $notion->asyncTasks()->wait($task);

        $this->assertTrue($task->isSucceeded());
        $this->assertSame("# Updated page", $task->pageMarkdown()->markdown);
        $this->assertCount(2, $this->history);
    }

    public function test_wait_failed_task(): void
    {
        $notion = $this->notion(self::json(AsyncTaskTest::taskArray("failed", [
            "error" => [
                "object" => "error",
                "status" => 400,
                "code" => "validation_error",
                "message" => "The request body was invalid.",
            ],
        ])));
        $task = AsyncTask::fromArray(AsyncTaskTest::taskArray("queued", [ "poll_after_seconds" => 0 ]));

        try {
            $notion->asyncTasks()->wait($task);
            $this->fail("Exception not thrown");
        } catch (AsyncTaskException $e) {
            $this->assertSame(
                "Async task task_abc123 failed (validation_error): The request body was invalid.",
                $e->getMessage(),
            );
            $this->assertTrue($e->task->isFailed());
        }
    }

    public function test_wait_timeout(): void
    {
        $notion = $this->notion();
        $task = AsyncTask::fromArray(AsyncTaskTest::taskArray("queued", [ "poll_after_seconds" => 10 ]));

        $this->expectException(AsyncTaskException::class);
        $this->expectExceptionMessage("did not finish within 5 seconds (status: queued)");
        $notion->asyncTasks()->wait($task, 5);
    }

    public function test_wait_finished_task_does_not_poll(): void
    {
        $notion = $this->notion();
        $task = AsyncTask::fromArray(AsyncTaskTest::taskArray("succeeded", [ "result" => [ "object" => "x" ] ]));

        $this->assertSame($task, $notion->asyncTasks()->wait($task));
        $this->assertCount(0, $this->history);
    }

    public function test_create_from_markdown_async_returns_task(): void
    {
        $notion = $this->notion(new Response(202, [], self::json(AsyncTaskTest::taskArray("queued", [
            "poll_after_seconds" => 2,
        ]))));

        $result = $notion->pages()->createFromMarkdownAsync(
            Page::create(PageParent::page("cf735738-35e3-44aa-b3d4-aca944c8f421")),
            "# Large page",
        );

        $this->assertInstanceOf(AsyncTask::class, $result);
        /** @var array<string, mixed> $body */
        $body = json_decode((string) $this->request(0)->getBody(), true);
        $this->assertTrue($body["allow_async"]);
        $this->assertSame("# Large page", $body["markdown"]);
    }

    public function test_create_from_markdown_async_returns_page(): void
    {
        $notion = $this->notion(self::json(AsyncTaskTest::pageArray()));

        $result = $notion->pages()->createFromMarkdownAsync(
            Page::create(PageParent::page("cf735738-35e3-44aa-b3d4-aca944c8f421")),
            "# Small page",
        );

        $this->assertInstanceOf(Page::class, $result);
    }

    public function test_create_from_markdown_does_not_allow_async(): void
    {
        $notion = $this->notion(self::json(AsyncTaskTest::pageArray()));

        $notion->pages()->createFromMarkdown(
            Page::create(PageParent::page("cf735738-35e3-44aa-b3d4-aca944c8f421")),
            "# Page",
        );

        /** @var array<string, mixed> $body */
        $body = json_decode((string) $this->request(0)->getBody(), true);
        $this->assertArrayNotHasKey("allow_async", $body);
    }

    public function test_update_markdown_async_returns_task(): void
    {
        $notion = $this->notion(new Response(202, [], self::json(AsyncTaskTest::taskArray("queued", [
            "poll_after_seconds" => 2,
        ]))));

        $result = $notion->pages()->updateMarkdownAsync("page-uuid", ReplaceContent::create("# Large content"));

        $this->assertInstanceOf(AsyncTask::class, $result);
        $request = $this->request(0);
        $this->assertSame("PATCH", $request->getMethod());
        $this->assertSame("https://api.notion.com/v1/pages/page-uuid/markdown", (string) $request->getUri());
        /** @var array<string, mixed> $body */
        $body = json_decode((string) $request->getBody(), true);
        $this->assertTrue($body["allow_async"]);
        $this->assertSame("replace_content", $body["type"]);
    }

    public function test_update_markdown_async_returns_markdown(): void
    {
        $notion = $this->notion(self::json(AsyncTaskTest::pageMarkdownArray()));

        $result = $notion->pages()->updateMarkdownAsync("page-uuid", ReplaceContent::create("# Content"));

        $this->assertInstanceOf(PageMarkdown::class, $result);
    }

    private function notion(Response|string ...$responses): Notion
    {
        $mock = new MockHandler(array_values(array_map(
            fn(Response|string $r) => is_string($r) ? new Response(200, [], $r) : $r,
            $responses,
        )));
        $stack = HandlerStack::create($mock);
        /** @psalm-suppress PossiblyInvalidPropertyAssignmentValue */
        $stack->push(Middleware::history($this->history));

        $config = Configuration::createFromPsrImplementations(
            "secret_123",
            new Client([ "handler" => $stack ]),
            new HttpFactory(),
        );

        return Notion::createFromConfig($config);
    }

    private function request(int $index): RequestInterface
    {
        /** @var array{request: RequestInterface} $transaction */
        $transaction = $this->history[$index];

        return $transaction["request"];
    }

    private static function json(array $array): string
    {
        return json_encode($array, JSON_THROW_ON_ERROR);
    }
}
