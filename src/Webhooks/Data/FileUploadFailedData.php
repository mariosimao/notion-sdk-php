<?php

namespace Notion\Webhooks\Data;

/**
 * Data of `file_upload.upload_failed` events.
 *
 * @psalm-import-type FileImportResultJson from FileImportResult
 *
 * @psalm-type FileUploadFailedDataJson = array{
 *     file_import_result: FileImportResultJson,
 * }
 *
 * @psalm-immutable
 */
final readonly class FileUploadFailedData implements EventData
{
    private function __construct(
        public FileImportResult $fileImportResult,
    ) {
    }

    /** @internal */
    public static function fromArray(array $array): self
    {
        /** @psalm-var FileUploadFailedDataJson $array */
        return new self(FileImportResult::fromArray($array["file_import_result"]));
    }
}
