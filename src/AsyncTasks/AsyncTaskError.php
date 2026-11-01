<?php

namespace Notion\AsyncTasks;

/**
 * @psalm-type AsyncTaskErrorJson = array{
 *      object: "error",
 *      status: int,
 *      code: string,
 *      message: string,
 *      additional_data?: array<string, string|list<string>>,
 * }
 *
 * @psalm-immutable
 */
final readonly class AsyncTaskError
{
    /** @param array<string, string|list<string>> $additionalData */
    private function __construct(
        public int $status,
        public string $code,
        public string $message,
        public array $additionalData,
    ) {
    }

    /**
     * @psalm-param AsyncTaskErrorJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["status"],
            $array["code"],
            $array["message"],
            $array["additional_data"] ?? [],
        );
    }

    /** @return AsyncTaskErrorJson */
    public function toArray(): array
    {
        $array = [
            "object" => "error",
            "status" => $this->status,
            "code" => $this->code,
            "message" => $this->message,
        ];

        if (!empty($this->additionalData)) {
            $array["additional_data"] = $this->additionalData;
        }

        return $array;
    }
}
