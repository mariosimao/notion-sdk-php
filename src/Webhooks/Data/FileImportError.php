<?php

namespace Notion\Webhooks\Data;

/**
 * @psalm-type FileImportErrorJson = array{
 *     type: string,
 *     code: string,
 *     message: string,
 *     parameter: string|null,
 *     status_code: int|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class FileImportError
{
    /** @param string $type One of `validation_error`, `internal_system_error`, `download_error` or `upload_error`. */
    private function __construct(
        public string $type,
        public string $code,
        public string $message,
        public string|null $parameter,
        public int|null $statusCode,
    ) {
    }

    /**
     * @psalm-param FileImportErrorJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["type"],
            $array["code"],
            $array["message"],
            $array["parameter"] ?? null,
            $array["status_code"] ?? null,
        );
    }
}
