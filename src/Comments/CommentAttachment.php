<?php

namespace Notion\Comments;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\Common\File;

/**
 * @psalm-type CommentAttachmentJson = array{
 *      category?: "audio"|"image"|"pdf"|"productivity"|"video",
 *      file?: array{ url: string, expiry_time?: string|null },
 *      type?: "file_upload",
 *      file_upload_id?: string
 * }
 *
 * @psalm-immutable
 */
final readonly class CommentAttachment
{
    private function __construct(
        public CommentAttachmentCategory|null $category,
        public File $file,
    ) {
    }

    /** Attach a file upload (with status "uploaded") when creating a comment */
    public static function fromFileUpload(string $fileUploadId): self
    {
        return new self(null, File::createFileUpload($fileUploadId));
    }

    /**
     * @param CommentAttachmentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        if (isset($array["file_upload_id"])) {
            return self::fromFileUpload($array["file_upload_id"]);
        }

        $category = isset($array["category"]) ? CommentAttachmentCategory::from($array["category"]) : null;
        $file = $array["file"] ?? [ "url" => "" ];
        $expiryTime = isset($file["expiry_time"]) ? new DateTimeImmutable($file["expiry_time"]) : null;

        return new self($category, File::createInternal($file["url"], $expiryTime));
    }

    public function toArray(): array
    {
        if ($this->file->isFileUpload()) {
            return [
                "type" => "file_upload",
                "file_upload_id" => $this->file->fileId,
            ];
        }

        $array = [];
        if ($this->category !== null) {
            $array["category"] = $this->category->value;
        }
        $array["file"] = [
            "url" => $this->file->url,
            "expiry_time" => $this->file->expiryTime?->format(Date::FORMAT),
        ];

        return $array;
    }

    public function isFileUpload(): bool
    {
        return $this->file->isFileUpload();
    }
}
