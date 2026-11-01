<?php

namespace Notion\Agents\Batch;

/**
 * Standard API error object returned by failed async tasks or batch operations
 *
 * @psalm-type TaskErrorJson = array{
 *     object?: "error",
 *     status?: int,
 *     code: string,
 *     message: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class TaskError
{
    private function __construct(
        public int|null $status,
        public string $code,
        public string $message,
    ) {
    }

    /**
     * @param TaskErrorJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["status"] ?? null,
            $array["code"],
            $array["message"],
        );
    }
}
