<?php

namespace Notion\Sessions;

/**
 * @psalm-type SessionErrorJson = array{
 *      code: string,
 *      message: string,
 *      retryable: bool,
 * }
 *
 * @psalm-immutable
 */
final readonly class SessionError
{
    private function __construct(
        public string $code,
        public string $message,
        public bool $retryable,
    ) {
    }

    /**
     * @param SessionErrorJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self($array["code"], $array["message"], $array["retryable"]);
    }

    /** @return SessionErrorJson */
    public function toArray(): array
    {
        return [
            "code" => $this->code,
            "message" => $this->message,
            "retryable" => $this->retryable,
        ];
    }
}
