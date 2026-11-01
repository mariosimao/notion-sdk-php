<?php

namespace Notion\AsyncTasks;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Exceptions\AsyncTaskException;
use Notion\Pages\Markdown\PageMarkdown;
use Notion\Pages\Page;

/**
 * @psalm-import-type AsyncTaskOperationJson from AsyncTaskOperation
 * @psalm-import-type AsyncTaskErrorJson from AsyncTaskError
 * @psalm-import-type PageJson from Page
 * @psalm-import-type PageMarkdownJson from PageMarkdown
 *
 * @psalm-type AsyncTaskJson = array{
 *      object: "async_task",
 *      id: string,
 *      status: "queued"|"running"|"retrying"|"succeeded"|"failed",
 *      status_url: string,
 *      created_time: string,
 *      operation: AsyncTaskOperationJson,
 *      poll_after_seconds?: int,
 *      result?: array<string, mixed>,
 *      error?: AsyncTaskErrorJson,
 * }
 */
final readonly class AsyncTask
{
    /** @param array<string, mixed>|null $result */
    private function __construct(
        public string $id,
        public AsyncTaskStatus $status,
        public string $statusUrl,
        public DateTimeImmutable $createdTime,
        public AsyncTaskOperation $operation,
        /** Minimum seconds to wait before polling again. Only set for non-terminal statuses. */
        public int|null $pollAfterSeconds,
        /** Raw operation result. Only set when the task succeeded. */
        public array|null $result,
        /** Only set when the task failed. */
        public AsyncTaskError|null $error,
    ) {
    }

    /**
     * @psalm-param AsyncTaskJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            AsyncTaskStatus::from($array["status"]),
            $array["status_url"],
            new DateTimeImmutable($array["created_time"]),
            AsyncTaskOperation::fromArray($array["operation"]),
            $array["poll_after_seconds"] ?? null,
            $array["result"] ?? null,
            isset($array["error"]) ? AsyncTaskError::fromArray($array["error"]) : null,
        );
    }

    /** @internal */
    public static function isAsyncTaskArray(array $array): bool
    {
        return ($array["object"] ?? null) === "async_task";
    }

    /** @return AsyncTaskJson */
    public function toArray(): array
    {
        $array = [
            "object" => "async_task",
            "id" => $this->id,
            "status" => $this->status->value,
            "status_url" => $this->statusUrl,
            "created_time" => $this->createdTime->format(Date::FORMAT),
            "operation" => $this->operation->toArray(),
        ];

        if ($this->pollAfterSeconds !== null) {
            $array["poll_after_seconds"] = $this->pollAfterSeconds;
        }
        if ($this->result !== null) {
            $array["result"] = $this->result;
        }
        if ($this->error !== null) {
            $array["error"] = $this->error->toArray();
        }

        return $array;
    }

    public function isPending(): bool
    {
        return !$this->status->isTerminal();
    }

    public function isSucceeded(): bool
    {
        return $this->status === AsyncTaskStatus::Succeeded;
    }

    public function isFailed(): bool
    {
        return $this->status === AsyncTaskStatus::Failed;
    }

    /**
     * Result of a succeeded `POST /v1/pages` task.
     *
     * @throws AsyncTaskException
     */
    public function page(): Page
    {
        /** @psalm-var PageJson $result */
        $result = $this->resultOf("page");

        return Page::fromArray($result);
    }

    /**
     * Result of a succeeded `PATCH /v1/pages/:page_id/markdown` task.
     *
     * @throws AsyncTaskException
     */
    public function pageMarkdown(): PageMarkdown
    {
        /** @psalm-var PageMarkdownJson $result */
        $result = $this->resultOf("page_markdown");

        return PageMarkdown::fromArray($result);
    }

    /** @return array<string, mixed> */
    private function resultOf(string $object): array
    {
        if ($this->result === null) {
            throw AsyncTaskException::notSucceeded($this);
        }

        if (($this->result["object"] ?? null) !== $object) {
            throw AsyncTaskException::unexpectedResult($this, $object);
        }

        return $this->result;
    }
}
