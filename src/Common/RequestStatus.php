<?php

namespace Notion\Common;

/**
 * Status of a query request (data source query or search)
 *
 * A query is marked as incomplete when Notion stops paginating before every
 * matching result was returned (e.g. after the 10,000 results limit).
 *
 * @psalm-type RequestStatusJson = array{
 *      type: "complete"|"incomplete",
 *      incomplete_reason?: "query_result_limit_reached"|null
 * }
 *
 * @psalm-immutable
 */
final readonly class RequestStatus
{
    private function __construct(
        public RequestStatusType $type,
        public IncompleteReason|null $incompleteReason,
    ) {
    }

    public static function complete(): self
    {
        return new self(RequestStatusType::Complete, null);
    }

    public static function incomplete(IncompleteReason $reason): self
    {
        return new self(RequestStatusType::Incomplete, $reason);
    }

    /** @param RequestStatusJson $array */
    public static function fromArray(array $array): self
    {
        $type = RequestStatusType::from($array["type"]);
        $reason = isset($array["incomplete_reason"])
            ? IncompleteReason::from($array["incomplete_reason"])
            : null;

        return new self($type, $type === RequestStatusType::Incomplete ? $reason : null);
    }

    /** @return RequestStatusJson */
    public function toArray(): array
    {
        $array = [ "type" => $this->type->value ];

        if ($this->incompleteReason !== null) {
            $array["incomplete_reason"] = $this->incompleteReason->value;
        }

        return $array;
    }

    public function isComplete(): bool
    {
        return $this->type === RequestStatusType::Complete;
    }

    public function isIncomplete(): bool
    {
        return $this->type === RequestStatusType::Incomplete;
    }

    public function isQueryResultLimitReached(): bool
    {
        return $this->incompleteReason === IncompleteReason::QueryResultLimitReached;
    }
}
