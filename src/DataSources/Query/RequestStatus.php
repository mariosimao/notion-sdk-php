<?php

namespace Notion\DataSources\Query;

/**
 * Status of a data source query response
 *
 * When a query reaches the per-query result limit (10,000 rows), Notion stops
 * paginating, sets `has_more` to `false` and marks the response as incomplete.
 *
 * @psalm-type RequestStatusJson = array{
 *      type: string,
 *      incomplete_reason?: string|null
 * }
 *
 * @psalm-immutable
 */
final readonly class RequestStatus
{
    public const TYPE_COMPLETE = "complete";
    public const TYPE_INCOMPLETE = "incomplete";

    public const REASON_QUERY_RESULT_LIMIT_REACHED = "query_result_limit_reached";

    private function __construct(
        public string $type,
        public string|null $incompleteReason,
    ) {
    }

    public static function complete(): self
    {
        return new self(self::TYPE_COMPLETE, null);
    }

    public static function incomplete(string $reason = self::REASON_QUERY_RESULT_LIMIT_REACHED): self
    {
        return new self(self::TYPE_INCOMPLETE, $reason);
    }

    /** @param RequestStatusJson $array */
    public static function fromArray(array $array): self
    {
        return new self($array["type"], $array["incomplete_reason"] ?? null);
    }

    public function toArray(): array
    {
        $array = [ "type" => $this->type ];

        if ($this->incompleteReason !== null) {
            $array["incomplete_reason"] = $this->incompleteReason;
        }

        return $array;
    }

    public function isComplete(): bool
    {
        return $this->type !== self::TYPE_INCOMPLETE;
    }

    public function isIncomplete(): bool
    {
        return $this->type === self::TYPE_INCOMPLETE;
    }

    public function isResultLimitReached(): bool
    {
        return $this->isIncomplete()
            && $this->incompleteReason === self::REASON_QUERY_RESULT_LIMIT_REACHED;
    }
}
