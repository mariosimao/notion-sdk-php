<?php

namespace Notion\FileUploads;

use DateTimeImmutable;

/**
 * @psalm-type FileUploadJson = array{
 *     object: "file_upload",
 *     id: string,
 *     created_time: string,
 *     last_edited_time: string,
 *     expiry_time: string|null,
 *     status: "pending"|"uploaded"|"expired"|"failed",
 *     filename: string|null,
 *     content_type: string|null,
 *     content_length: int|null,
 *     upload_url?: string,
 *     complete_url?: string,
 *     file_import_result?: string,
 * }
 */
class FileUpload
{
    private function __construct(
        public readonly string $id,
        public readonly DateTimeImmutable $createdTime,
        public readonly DateTimeImmutable $lastEditedTime,
        public readonly DateTimeImmutable|null $expiryTime,
        public readonly FileUploadStatus $status,
        public readonly string|null $filename,
        public readonly string|null $contentType,
        public readonly int|null $contentLength,
        public readonly string|null $uploadUrl,
        public readonly string|null $completeUrl,
        public readonly string|null $fileImportResult,
    ) {
    }

    /**
     * @param FileUploadJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["id"],
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            isset($array["expiry_time"]) ? new DateTimeImmutable($array["expiry_time"]) : null,
            FileUploadStatus::from($array["status"]),
            $array["filename"] ?? null,
            $array["content_type"] ?? null,
            $array["content_length"] ?? null,
            $array["upload_url"] ?? null,
            $array["complete_url"] ?? null,
            $array["file_import_result"] ?? null,
        );
    }

    public function isAttached(): bool
    {
        return $this->status == FileUploadStatus::Uploaded &&
            $this->expiryTime !== null;
    }
}
