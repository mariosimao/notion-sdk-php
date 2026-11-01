<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-import-type PropertyConfigurationJson from PropertyConfiguration
 * @psalm-import-type GroupByJson from GroupBy
 * @psalm-import-type CoverJson from Cover
 *
 * @psalm-type BoardConfigurationJson = array{
 *      type: "board",
 *      group_by: GroupByJson,
 *      sub_group_by?: GroupByJson|null,
 *      properties?: PropertyConfigurationJson[]|null,
 *      cover?: CoverJson|null,
 *      cover_size?: string|null,
 *      cover_aspect?: string|null,
 *      card_layout?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class BoardConfiguration implements ViewConfiguration
{
    /** @param PropertyConfiguration[] $properties */
    private function __construct(
        public GroupBy $groupBy,
        public GroupBy|null $subGroupBy,
        public array $properties,
        public Cover|null $cover,
        public CoverSize|null $coverSize,
        public CoverAspect|null $coverAspect,
        public CardLayout|null $cardLayout,
    ) {
    }

    /** @param GroupBy $groupBy Property used to create the board columns */
    public static function create(GroupBy $groupBy): self
    {
        return new self($groupBy, null, [], null, null, null, null);
    }

    /**
     * @param BoardConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            GroupBy::fromArray($array["group_by"]),
            isset($array["sub_group_by"]) ? GroupBy::fromArray($array["sub_group_by"]) : null,
            array_map(PropertyConfiguration::fromArray(...), $array["properties"] ?? []),
            isset($array["cover"]) ? Cover::fromArray($array["cover"]) : null,
            isset($array["cover_size"]) ? CoverSize::from($array["cover_size"]) : null,
            isset($array["cover_aspect"]) ? CoverAspect::from($array["cover_aspect"]) : null,
            isset($array["card_layout"]) ? CardLayout::from($array["card_layout"]) : null,
        );
    }

    public function type(): ViewType
    {
        return ViewType::Board;
    }

    public function toArray(): array
    {
        return [
            "type"         => $this->type()->value,
            "group_by"     => $this->groupBy->toArray(),
            "sub_group_by" => $this->subGroupBy?->toArray(),
            "properties"   => PropertyConfigurations::toArray($this->properties),
            "cover"        => $this->cover?->toArray(),
            "cover_size"   => $this->coverSize?->value,
            "cover_aspect" => $this->coverAspect?->value,
            "card_layout"  => $this->cardLayout?->value,
        ];
    }

    public function changeGroupBy(GroupBy $groupBy): self
    {
        return $this->with(groupBy: $groupBy);
    }

    public function changeSubGroupBy(GroupBy $subGroupBy): self
    {
        return $this->with(subGroupBy: $subGroupBy);
    }

    public function removeSubGroupBy(): self
    {
        return $this->with(subGroupBy: null);
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
