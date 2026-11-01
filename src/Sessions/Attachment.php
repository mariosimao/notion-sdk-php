<?php

namespace Notion\Sessions;

use Notion\FileUploads\FileUpload;

/**
 * A file sent alongside a message. Upload it first with
 * `Notion\FileUploads\Client::upload()`.
 *
 * @psalm-type AttachmentJson = array{
 *      type: "file_upload",
 *      file_upload: array{ id: string },
 *      name?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Attachment
{
    private function __construct(
        public string $fileUploadId,
        public string|null $name,
    ) {
    }

    public static function fileUpload(string $fileUploadId, string|null $name = null): self
    {
        return new self($fileUploadId, $name);
    }

    public static function fromFileUpload(FileUpload $fileUpload, string|null $name = null): self
    {
        return new self($fileUpload->id, $name);
    }

    /** @return AttachmentJson */
    public function toArray(): array
    {
        $array = [
            "type" => "file_upload",
            "file_upload" => [ "id" => $this->fileUploadId ],
        ];

        if ($this->name !== null) {
            $array["name"] = $this->name;
        }

        return $array;
    }
}
