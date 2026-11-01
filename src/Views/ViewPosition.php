<?php

namespace Notion\Views;

/**
 * Position of a new view in the database view tabs
 *
 * @psalm-immutable
 */
final readonly class ViewPosition
{
    private function __construct(
        private string $type,
        private string|null $viewId,
    ) {
    }

    public static function start(): self
    {
        return new self("start", null);
    }

    public static function end(): self
    {
        return new self("end", null);
    }

    public static function afterView(string $viewId): self
    {
        return new self("after_view", $viewId);
    }

    public function toArray(): array
    {
        $array = [ "type" => $this->type ];

        if ($this->viewId !== null) {
            $array["view_id"] = $this->viewId;
        }

        return $array;
    }
}
