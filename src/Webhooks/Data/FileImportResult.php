<?php

namespace Notion\Webhooks\Data;

use DateTimeImmutable;

/**
 * @psalm-import-type FileImportErrorJson from FileImportError
 *
 * @psalm-type FileImportResultJson = array{
 *     type: "success"|"error",
 *     imported_time: string,
 *     success?: array<empty, empty>,
 *     error?: FileImportErrorJson,
 * }
 *
 * @psalm-immutable
 */
final readonly class FileImportResult
{
    private function __construct(
        public DateTimeImmutable $importedTime,
        public FileImportError|null $error,
    ) {
    }

    /**
     * @psalm-param FileImportResultJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $error = $array["type"] === "error" && isset($array["error"])
            ? FileImportError::fromArray($array["error"])
            : null;

        return new self(new DateTimeImmutable($array["imported_time"]), $error);
    }

    /** @psalm-assert-if-false FileImportError $this->error */
    public function isSuccess(): bool
    {
        return $this->error === null;
    }
}
