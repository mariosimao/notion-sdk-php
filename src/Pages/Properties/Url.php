<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type UrlJson = array{
 *      id: string,
 *      type: "url",
 *      url: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class Url implements PropertyInterface
{
    private function __construct(
        private PropertyMetadata $metadata,
        public string|null $url
    ) {
    }

    public static function create(string $url): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Url);

        return new self($metadata, $url);
    }

    public static function createEmpty(): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Url);

        return new self($metadata, null);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var UrlJson $array */

        $metadata = PropertyMetadata::fromArray($array);

        $url = $array["url"];

        return new self($metadata, $url);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["url"] = $this->url;

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeUrl(string $url): self
    {
        return new self($this->metadata, $url);
    }

    public function clear(): self
    {
        return new self($this->metadata, null);
    }

    public function isEmpty(): bool
    {
        return $this->url === null;
    }
}
