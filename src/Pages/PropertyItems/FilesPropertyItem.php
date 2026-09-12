<?php

namespace Notion\Pages\PropertyItems;

use Notion\Common\File;
use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type FilesItemJson = array{
 *      id: string,
 *      type: "files",
 *      files: FileJson[],
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class FilesPropertyItem implements PropertyItemInterface
{
    /** @param list<File> $files */
    private function __construct(
        private PropertyItemMetadata $metadata,
        public array $files,
    ) {
    }

    /** @param list<File> $files */
    public static function create(array $files, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Files);

        return new self($metadata, $files);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var FilesItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        /** @var list<File> $files */
        $files = array_map(fn(array $f) => File::fromArray($f), $array["files"] ?? []);

        return new self($metadata, $files);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["files"] = array_map(fn(File $f) => $f->toArray(), $this->files);

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return empty($this->files);
    }
}
