<?php

namespace Notion\Sessions\Events;

/**
 * @psalm-type TokenUsageJson = array{
 *      total_tokens: int,
 *      input_tokens?: int,
 *      output_tokens?: int,
 * }
 *
 * @psalm-immutable
 */
final readonly class TokenUsage
{
    private function __construct(
        public int $totalTokens,
        public int|null $inputTokens,
        public int|null $outputTokens,
    ) {
    }

    /**
     * @param TokenUsageJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["total_tokens"],
            $array["input_tokens"] ?? null,
            $array["output_tokens"] ?? null,
        );
    }

    /** @return TokenUsageJson */
    public function toArray(): array
    {
        $array = [ "total_tokens" => $this->totalTokens ];
        if ($this->inputTokens !== null) {
            $array["input_tokens"] = $this->inputTokens;
        }
        if ($this->outputTokens !== null) {
            $array["output_tokens"] = $this->outputTokens;
        }

        return $array;
    }
}
