<?php

namespace Notion\AsyncTasks;

/**
 * @psalm-type AsyncTaskOperationJson = array{
 *      surface: "rest"|"mcp",
 *      name: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class AsyncTaskOperation
{
    private function __construct(
        public AsyncTaskSurface $surface,
        /** Operation that created the task, e.g. "PATCH /v1/pages/:page_id/markdown". */
        public string $name,
    ) {
    }

    /**
     * @psalm-param AsyncTaskOperationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(AsyncTaskSurface::from($array["surface"]), $array["name"]);
    }

    /** @return AsyncTaskOperationJson */
    public function toArray(): array
    {
        return [
            "surface" => $this->surface->value,
            "name" => $this->name,
        ];
    }
}
