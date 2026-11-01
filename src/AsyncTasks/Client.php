<?php

namespace Notion\AsyncTasks;

use Notion\Configuration;
use Notion\Exceptions\AsyncTaskException;
use Notion\Infrastructure\Http;

/**
 * @psalm-import-type AsyncTaskJson from AsyncTask
 */
final readonly class Client
{
    /**
     * @internal Use `\Notion\Notion::asyncTasks()` instead
     */
    public function __construct(
        private Configuration $config,
    ) {
    }

    public function find(string $taskId): AsyncTask
    {
        $url = "https://api.notion.com/v1/async_tasks/" . rawurlencode($taskId);
        $request = Http::createRequest($url, $this->config);

        /** @psalm-var AsyncTaskJson $body */
        $body = Http::sendRequest($request, $this->config);

        return AsyncTask::fromArray($body);
    }

    /**
     * Polls the task, honoring `poll_after_seconds`, until it succeeds.
     *
     * @throws AsyncTaskException When the task fails or does not finish within `$timeoutSeconds`.
     */
    public function wait(AsyncTask $task, int $timeoutSeconds = 60): AsyncTask
    {
        $deadline = time() + $timeoutSeconds;

        while ($task->isPending()) {
            $delay = $task->pollAfterSeconds ?? 1;
            if (time() + $delay > $deadline) {
                throw AsyncTaskException::timedOut($task, $timeoutSeconds);
            }

            if ($delay > 0) {
                sleep($delay);
            }

            $task = $this->find($task->id);
        }

        if ($task->isFailed()) {
            throw AsyncTaskException::failed($task);
        }

        return $task;
    }
}
