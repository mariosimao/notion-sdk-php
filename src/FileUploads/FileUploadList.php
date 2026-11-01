<?php

namespace Notion\FileUploads;

/**
 * A single page of file uploads returned by the Notion API.
 *
 * @psalm-import-type FileUploadJson from FileUpload
 *
 * @psalm-type FileUploadListJson = array{
 *      results: list<FileUploadJson>,
 *      next_cursor: string|null,
 *      has_more: bool,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class FileUploadList
{
    /** @param list<FileUpload> $results */
    private function __construct(
        public array $results,
        public string|null $nextCursor,
        public bool $hasMore,
    ) {
    }

    /** @param list<FileUpload> $results */
    public static function create(array $results, string|null $nextCursor = null, bool $hasMore = false): self
    {
        return new self($results, $nextCursor, $hasMore);
    }

    /**
     * @psalm-param FileUploadListJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $results = array_map(
            fn(array $fileUpload): FileUpload => FileUpload::fromArray($fileUpload),
            $array["results"],
        );

        return new self($results, $array["next_cursor"], $array["has_more"]);
    }
}
