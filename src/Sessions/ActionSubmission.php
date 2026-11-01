<?php

namespace Notion\Sessions;

/**
 * Answers to the actions a session requires before it can continue.
 *
 * @psalm-type ActionSubmissionJson = array{
 *      session_id: string,
 *      actions: list<array{ action_id: string, option_id: "approve"|"reject" }>,
 *      metadata?: array<string, string>,
 * }
 *
 * @psalm-immutable
 */
final readonly class ActionSubmission
{
    /**
     * @param array<string, ActionOption> $actions Option chosen, indexed by action ID
     * @param array<string, string> $metadata
     */
    private function __construct(
        public string $sessionId,
        public array $actions,
        public array $metadata,
    ) {
    }

    public static function create(string $sessionId): self
    {
        return new self($sessionId, [], []);
    }

    public function answer(string $actionId, ActionOption $option): self
    {
        $actions = $this->actions;
        $actions[$actionId] = $option;

        return new self($this->sessionId, $actions, $this->metadata);
    }

    public function approve(string|RequiredAction $action): self
    {
        $actionId = $action instanceof RequiredAction ? $action->actionId : $action;

        return $this->answer($actionId, ActionOption::Approve);
    }

    public function reject(string|RequiredAction $action): self
    {
        $actionId = $action instanceof RequiredAction ? $action->actionId : $action;

        return $this->answer($actionId, ActionOption::Reject);
    }

    /** @param array<string, string> $metadata */
    public function changeMetadata(array $metadata): self
    {
        return new self($this->sessionId, $this->actions, $metadata);
    }

    /**
     * @internal
     *
     * @return ActionSubmissionJson
     */
    public function toArray(): array
    {
        $actions = [];
        foreach ($this->actions as $actionId => $option) {
            /** @psalm-suppress RedundantCastGivenDocblockType Numeric keys are converted to int by PHP */
            $actions[] = [
                "action_id" => (string) $actionId,
                "option_id" => $option->value,
            ];
        }

        $array = [
            "session_id" => $this->sessionId,
            "actions" => $actions,
        ];

        if ($this->metadata !== []) {
            $array["metadata"] = $this->metadata;
        }

        return $array;
    }
}
