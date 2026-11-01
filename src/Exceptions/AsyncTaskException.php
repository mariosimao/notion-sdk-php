<?php

namespace Notion\Exceptions;

use Notion\AsyncTasks\AsyncTask;

final class AsyncTaskException extends NotionException
{
    private function __construct(
        string $message,
        public readonly AsyncTask $task,
    ) {
        parent::__construct($message);
    }

    public static function failed(AsyncTask $task): self
    {
        $code = $task->error?->code ?? "unknown";
        $message = $task->error?->message ?? "";

        return new self("Async task {$task->id} failed ({$code}): {$message}", $task);
    }

    public static function timedOut(AsyncTask $task, int $timeoutSeconds): self
    {
        return new self(
            "Async task {$task->id} did not finish within {$timeoutSeconds} seconds (status: {$task->status->value}).",
            $task,
        );
    }

    public static function notSucceeded(AsyncTask $task): self
    {
        return new self(
            "Async task {$task->id} has no result because it has not succeeded (status: {$task->status->value}).",
            $task,
        );
    }

    public static function unexpectedResult(AsyncTask $task, string $expectedObject): self
    {
        $object = (string) ($task->result["object"] ?? "unknown");

        return new self(
            "Async task {$task->id} result is a \"{$object}\" object, expected \"{$expectedObject}\".",
            $task,
        );
    }
}
