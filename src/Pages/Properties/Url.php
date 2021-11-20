<?php

namespace Notion\Pages\Properties;

/**
 * @psalm-type UrlJson = array{
 *      id: string,
 *      type: "url",
 *      url: string,
 * }
 *
 * @psalm-immutable
 */
class Url implements PropertyInterface
{
    private const TYPE = Property::TYPE_URL;

    private Property $property;

    private string $url;

    private function __construct(Property $property, string $url)
    {
        $this->property = $property;
        $this->url = $url;
    }

    public static function create(string $url): self
    {
        $property = Property::create("", self::TYPE);

        return new self($property, $url);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var UrlJson $array */

        $property = Property::fromArray($array);

        $url = $array[self::TYPE];

        return new self($property, $url);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = $this->url;

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function withUrl(string $url): self
    {
        return new self($this->property, $url);
    }
}
