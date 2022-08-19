<?php

namespace Notion\Pages\Properties;

use Notion\Common\File;

/**
 * @psalm-import-type FileJson from \Notion\Common\File
 *
 * @psalm-type FilesJson = array{
 *      id: string,
 *      type: "files",
 *      files: FileJson[],
 * }
 *
 * @psalm-immutable
 */
class Files implements PropertyInterface
{
    private const TYPE = Property::TYPE_FILES;

    private Property $property;

    /** @var File[] */
    private array $files;

    /** @param File[] $files */
    private function __construct(Property $property, array $files)
    {
        $this->property = $property;
        $this->files = $files;
    }

    /** @param File[] $files */
    public static function create(array $files): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, $files);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var FilesJson $array */

        $property = Property::fromArray($array);

        $files = array_map(fn($f) => File::fromArray($f), $array[self::TYPE]);

        return new self($property, $files);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = array_map(fn($f) => $f->toArray(), $this->files);

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    /** @return File[] */
    public function files(): array
    {
        return $this->files;
    }

    /** @param File[] $files */
    public function withFiles(array $files): self
    {
        return new self($this->property, $files);
    }

    public function withAddedFile(File $file): self
    {
        $files = array_merge($this->files, [$file]);

        return new self($this->property, $files);
    }
}
