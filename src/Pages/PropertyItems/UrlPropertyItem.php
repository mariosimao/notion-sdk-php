<?php

namespace Notion\Pages\PropertyItems;

use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type UrlItemJson = array{
 *      id: string,
 *      type: "url",
 *      url: string|null,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class UrlPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public string|null $url,
    ) {
    }

    public static function create(string|null $url, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Url);

        return new self($metadata, $url);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var UrlItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $url = $array["url"] ?? null;

        return new self($metadata, $url);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["url"] = $this->url;

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return $this->url === null;
    }
}
