<?php

namespace Notion\Users;

/**
 * @psalm-type WorkspaceLimitsJson = array{
 *     max_file_upload_size_in_bytes: int,
 * }
 *
 * @psalm-immutable
 */
class WorkspaceLimits
{
    private function __construct(
        public readonly int $maxFileUploadSizeInBytes
    ) {
    }

    /**
     * @param WorkspaceLimitsJson $array
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array["max_file_upload_size_in_bytes"] ?? 0,
        );
    }

    /** @return WorkspaceLimitsJson */
    public function toArray(): array
    {
        return [
            "max_file_upload_size_in_bytes" => $this->maxFileUploadSizeInBytes
        ];
    }
}
