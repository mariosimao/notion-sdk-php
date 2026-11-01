<?php

namespace Notion\Test\Unit\AsyncTasks;

use Notion\AsyncTasks\AsyncTask;
use Notion\AsyncTasks\AsyncTaskStatus;
use Notion\AsyncTasks\AsyncTaskSurface;
use Notion\Exceptions\AsyncTaskException;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-import-type AsyncTaskJson from AsyncTask
 * @psalm-import-type PageJson from \Notion\Pages\Page
 * @psalm-import-type PageMarkdownJson from \Notion\Pages\Markdown\PageMarkdown
 */
class AsyncTaskTest extends TestCase
{
    public function test_queued_task(): void
    {
        $task = AsyncTask::fromArray(self::taskArray("queued", [ "poll_after_seconds" => 2 ]));

        $this->assertSame("task_abc123", $task->id);
        $this->assertSame(AsyncTaskStatus::Queued, $task->status);
        $this->assertSame("https://api.notion.com/v1/async_tasks/task_abc123", $task->statusUrl);
        $this->assertSame("2026-06-29T12:00:00+00:00", $task->createdTime->format(DATE_ATOM));
        $this->assertSame(AsyncTaskSurface::Rest, $task->operation->surface);
        $this->assertSame("PATCH /v1/pages/:page_id/markdown", $task->operation->name);
        $this->assertSame(2, $task->pollAfterSeconds);
        $this->assertNull($task->result);
        $this->assertNull($task->error);
        $this->assertTrue($task->isPending());
        $this->assertFalse($task->isSucceeded());
        $this->assertFalse($task->isFailed());
    }

    public function test_succeeded_task_with_page_markdown_result(): void
    {
        $task = AsyncTask::fromArray(self::taskArray("succeeded", [ "result" => self::pageMarkdownArray() ]));

        $this->assertTrue($task->isSucceeded());
        $this->assertFalse($task->isPending());
        $this->assertNull($task->pollAfterSeconds);

        $markdown = $task->pageMarkdown();
        $this->assertSame("page-uuid", $markdown->id);
        $this->assertSame("# Updated page", $markdown->markdown);
    }

    public function test_succeeded_task_with_page_result(): void
    {
        $task = AsyncTask::fromArray(self::taskArray("succeeded", [ "result" => self::pageArray() ]));

        $page = $task->page();

        $this->assertSame("ff747ce6-bb89-4c54-80c3-a248a2c78bd9", $page->id);
    }

    public function test_failed_task(): void
    {
        $task = AsyncTask::fromArray(self::taskArray("failed", [
            "error" => [
                "object" => "error",
                "status" => 400,
                "code" => "validation_error",
                "message" => "The request body was invalid.",
                "additional_data" => [ "field" => "markdown" ],
            ],
        ]));

        $this->assertTrue($task->isFailed());
        $this->assertFalse($task->isPending());
        $this->assertSame(400, $task->error?->status);
        $this->assertSame("validation_error", $task->error?->code);
        $this->assertSame("The request body was invalid.", $task->error?->message);
        $this->assertSame([ "field" => "markdown" ], $task->error?->additionalData);
    }

    public function test_result_of_unfinished_task(): void
    {
        $task = AsyncTask::fromArray(self::taskArray("running", [ "poll_after_seconds" => 2 ]));

        $this->expectException(AsyncTaskException::class);
        $this->expectExceptionMessage("has not succeeded (status: running)");
        $task->pageMarkdown();
    }

    public function test_unexpected_result_type(): void
    {
        $task = AsyncTask::fromArray(self::taskArray("succeeded", [ "result" => self::pageMarkdownArray() ]));

        $this->expectException(AsyncTaskException::class);
        $this->expectExceptionMessage("result is a \"page_markdown\" object, expected \"page\"");
        $task->page();
    }

    public function test_array_conversion(): void
    {
        $arrays = [
            self::taskArray("retrying", [ "poll_after_seconds" => 5 ]),
            self::taskArray("succeeded", [ "result" => self::pageMarkdownArray() ]),
            self::taskArray("failed", [
                "error" => [ "object" => "error", "status" => 404, "code" => "object_not_found", "message" => "x" ],
            ]),
        ];

        foreach ($arrays as $array) {
            $this->assertEquals($array, AsyncTask::fromArray($array)->toArray());
        }
    }

    public function test_is_async_task_array(): void
    {
        $this->assertTrue(AsyncTask::isAsyncTaskArray(self::taskArray("queued")));
        $this->assertFalse(AsyncTask::isAsyncTaskArray(self::pageMarkdownArray()));
    }

    public function test_terminal_statuses(): void
    {
        $this->assertFalse(AsyncTaskStatus::Queued->isTerminal());
        $this->assertFalse(AsyncTaskStatus::Running->isTerminal());
        $this->assertFalse(AsyncTaskStatus::Retrying->isTerminal());
        $this->assertTrue(AsyncTaskStatus::Succeeded->isTerminal());
        $this->assertTrue(AsyncTaskStatus::Failed->isTerminal());
    }

    /**
     * @param "queued"|"running"|"retrying"|"succeeded"|"failed" $status
     * @param array<string, mixed> $extra
     *
     * @return AsyncTaskJson
     */
    public static function taskArray(string $status, array $extra = []): array
    {
        $array = [
            "object" => "async_task",
            "id" => "task_abc123",
            "status" => $status,
            "status_url" => "https://api.notion.com/v1/async_tasks/task_abc123",
            "created_time" => "2026-06-29T12:00:00.000000Z",
            "operation" => [
                "surface" => "rest",
                "name" => "PATCH /v1/pages/:page_id/markdown",
            ],
            ...$extra,
        ];

        /** @psalm-var AsyncTaskJson $array */
        return $array;
    }

    /** @return PageMarkdownJson */
    public static function pageMarkdownArray(): array
    {
        return [
            "object" => "page_markdown",
            "id" => "page-uuid",
            "markdown" => "# Updated page",
            "truncated" => false,
            "unknown_block_ids" => [],
        ];
    }

    /** @return PageJson */
    public static function pageArray(): array
    {
        $array = [
            "object" => "page",
            "id" => "ff747ce6-bb89-4c54-80c3-a248a2c78bd9",
            "created_time" => "2023-01-11T21:04:00.000Z",
            "last_edited_time" => "2023-01-11T21:04:00.000Z",
            "created_by" => [ "object" => "user", "id" => "e8f2d77a-8756-43f6-bc87-0dc2bf9115fa" ],
            "last_edited_by" => [ "object" => "user", "id" => "e8f2d77a-8756-43f6-bc87-0dc2bf9115fa" ],
            "cover" => null,
            "icon" => null,
            "parent" => [ "type" => "page_id", "page_id" => "cf735738-35e3-44aa-b3d4-aca944c8f421" ],
            "in_trash" => false,
            "properties" => [ "title" => [ "id" => "title", "type" => "title", "title" => [] ] ],
            "url" => "https://www.notion.so/ff747ce6bb894c5480c3a248a2c78bd9",
        ];

        /** @psalm-var PageJson $array */
        return $array;
    }
}
