<?php

namespace Notion\Databases\Properties;

/**
 * @psalm-type FilesJson = array{
 *      id: string,
 *      name: string,
 *      type: "files",
 *      file: array<empty, empty>,
 * }
 *
 * @psalm-immutable
 */
class Files implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
    ) {
    }

    public static function create(string $propertyName = "Files"): self
    {
        $metadata = PropertyMetadata::create("", $propertyName, PropertyType::Files);

        return new self($metadata);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var FilesJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        return new self($metadata);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["files"] = new \stdClass();

        return $array;
    }
}
