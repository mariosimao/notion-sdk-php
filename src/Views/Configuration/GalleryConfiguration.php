<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-import-type PropertyConfigurationJson from PropertyConfiguration
 * @psalm-import-type CoverJson from Cover
 *
 * @psalm-type GalleryConfigurationJson = array{
 *      type: "gallery",
 *      properties?: PropertyConfigurationJson[]|null,
 *      cover?: CoverJson|null,
 *      cover_size?: string|null,
 *      cover_aspect?: string|null,
 *      card_layout?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class GalleryConfiguration implements ViewConfiguration
{
    /** @param PropertyConfiguration[] $properties */
    private function __construct(
        public array $properties,
        public Cover|null $cover,
        public CoverSize|null $coverSize,
        public CoverAspect|null $coverAspect,
        public CardLayout|null $cardLayout,
    ) {
    }

    public static function create(): self
    {
        return new self([], null, null, null, null);
    }

    /**
     * @param GalleryConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            array_map(PropertyConfiguration::fromArray(...), $array["properties"] ?? []),
            isset($array["cover"]) ? Cover::fromArray($array["cover"]) : null,
            isset($array["cover_size"]) ? CoverSize::from($array["cover_size"]) : null,
            isset($array["cover_aspect"]) ? CoverAspect::from($array["cover_aspect"]) : null,
            isset($array["card_layout"]) ? CardLayout::from($array["card_layout"]) : null,
        );
    }

    public function type(): ViewType
    {
        return ViewType::Gallery;
    }

    public function toArray(): array
    {
        return [
            "type"         => $this->type()->value,
            "properties"   => PropertyConfigurations::toArray($this->properties),
            "cover"        => $this->cover?->toArray(),
            "cover_size"   => $this->coverSize?->value,
            "cover_aspect" => $this->coverAspect?->value,
            "card_layout"  => $this->cardLayout?->value,
        ];
    }

    /** Replace all property configurations */
    public function changeProperties(PropertyConfiguration ...$properties): self
    {
        return $this->with(properties: $properties);
    }

    /** Add a property configuration, replacing any existing one for the same property */
    public function addProperty(PropertyConfiguration $property): self
    {
        return $this->with(properties: PropertyConfigurations::add($this->properties, $property));
    }

    public function changeCover(Cover $cover): self
    {
        return $this->with(cover: $cover);
    }

    public function removeCover(): self
    {
        return $this->with(cover: null, coverSize: null, coverAspect: null);
    }

    public function changeCoverSize(CoverSize $coverSize): self
    {
        return $this->with(coverSize: $coverSize);
    }

    public function changeCoverAspect(CoverAspect $coverAspect): self
    {
        return $this->with(coverAspect: $coverAspect);
    }

    public function changeCardLayout(CardLayout $cardLayout): self
    {
        return $this->with(cardLayout: $cardLayout);
    }

    /** @psalm-suppress MixedArgument */
    private function with(mixed ...$changes): self
    {
        return new self(...[ ...get_object_vars($this), ...$changes ]);
    }
}
