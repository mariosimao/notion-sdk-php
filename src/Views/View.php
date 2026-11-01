<?php

namespace Notion\Views;

use DateTimeImmutable;
use Notion\Common\Date;
use Notion\DataSources\Query\Filter;
use Notion\DataSources\Query\RawFilter;
use Notion\DataSources\Query\Sort;
use Notion\Exceptions\ViewException;
use Notion\Users\User;
use Notion\Views\Configuration\DashboardConfiguration;
use Notion\Views\Configuration\ViewConfiguration;
use Notion\Views\Configuration\ViewConfigurationFactory;

/**
 * @psalm-import-type ViewParentJson from ViewParent
 * @psalm-import-type UserJson from \Notion\Users\User
 * @psalm-import-type SortJson from \Notion\DataSources\Query\Sort
 * @psalm-import-type ViewConfigurationJson from \Notion\Views\Configuration\ViewConfigurationFactory
 *
 * @psalm-type ViewJson = array{
 *      object: "view",
 *      id: string,
 *      parent?: ViewParentJson|null,
 *      data_source_id?: string|null,
 *      name: string,
 *      type: string,
 *      filter?: array|null,
 *      sorts?: list<SortJson>|null,
 *      quick_filters?: array<array-key, array>|null,
 *      configuration?: ViewConfigurationJson|null,
 *      created_time: string,
 *      last_edited_time: string,
 *      created_by?: UserJson|null,
 *      last_edited_by?: UserJson|null,
 *      url?: string|null,
 *      dashboard_view_id?: string|null,
 * }
 *
 * @psalm-immutable
 */
final readonly class View
{
    /**
     * @param Sort[] $sorts
     * @param QuickFilter[] $quickFilters
     * @param string[] $removedQuickFilters
     */
    private function __construct(
        public string $id,
        public ViewParent|null $parent,
        public string|null $dataSourceId,
        public string $name,
        public ViewType $type,
        public Filter|null $filter,
        public array $sorts,
        public array $quickFilters,
        public ViewConfiguration|null $configuration,
        public DateTimeImmutable $createdTime,
        public DateTimeImmutable $lastEditedTime,
        public User|null $createdBy,
        public User|null $lastEditedBy,
        public string|null $url,
        public string|null $dashboardViewId,
        private array $removedQuickFilters,
    ) {
    }

    /**
     * Create a new view. Use `\Notion\Notion::views()->create()` to persist it.
     */
    public static function create(string $dataSourceId, string $name, ViewType $type): self
    {
        $now = new DateTimeImmutable("now");

        return new self(
            "",
            null,
            $dataSourceId,
            $name,
            $type,
            null,
            [],
            [],
            null,
            $now,
            $now,
            null,
            null,
            null,
            null,
            [],
        );
    }

    /**
     * @param ViewJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $quickFilters = [];
        foreach ($array["quick_filters"] ?? [] as $property => $condition) {
            $quickFilters[] = QuickFilter::fromArray((string) $property, $condition);
        }

        return new self(
            $array["id"],
            isset($array["parent"]) ? ViewParent::fromArray($array["parent"]) : null,
            $array["data_source_id"] ?? null,
            $array["name"],
            ViewType::from($array["type"]),
            isset($array["filter"]) ? RawFilter::fromArray($array["filter"]) : null,
            array_map(Sort::fromArray(...), $array["sorts"] ?? []),
            $quickFilters,
            isset($array["configuration"]) ? ViewConfigurationFactory::fromArray($array["configuration"]) : null,
            new DateTimeImmutable($array["created_time"]),
            new DateTimeImmutable($array["last_edited_time"]),
            isset($array["created_by"]) ? User::fromArray($array["created_by"]) : null,
            isset($array["last_edited_by"]) ? User::fromArray($array["last_edited_by"]) : null,
            $array["url"] ?? null,
            $array["dashboard_view_id"] ?? null,
            [],
        );
    }

    public function toArray(): array
    {
        $array = [
            "object"           => "view",
            "id"               => $this->id,
            "parent"           => $this->parent?->toArray(),
            "data_source_id"   => $this->dataSourceId,
            "name"             => $this->name,
            "type"             => $this->type->value,
            "filter"           => $this->filter?->toArray(),
            "sorts"            => $this->sorts === [] ? null : $this->sortsToArray(),
            "quick_filters"    => $this->quickFilters === [] ? null : $this->quickFiltersToArray(),
            "configuration"    => $this->configuration?->toArray(),
            "created_time"     => $this->createdTime->format(Date::FORMAT),
            "last_edited_time" => $this->lastEditedTime->format(Date::FORMAT),
            "created_by"       => $this->createdBy?->toArray(),
            "last_edited_by"   => $this->lastEditedBy?->toArray(),
            "url"              => $this->url,
        ];

        if ($this->dashboardViewId !== null) {
            $array["dashboard_view_id"] = $this->dashboardViewId;
        }

        return $array;
    }

    /** @return list<array> */
    public function sortsToArray(): array
    {
        return array_values(array_map(fn (Sort $s) => $s->toArray(), $this->sorts));
    }

    /**
     * Quick filters in the API format, keyed by property.
     *
     * @return array<string, array|object>
     */
    public function quickFiltersToArray(): array
    {
        $array = [];
        foreach ($this->quickFilters as $quickFilter) {
            $array[$quickFilter->property] = $quickFilter->toArray();
        }

        return $array;
    }

    /**
     * Quick filters removed since the view was retrieved
     *
     * @internal
     *
     * @return string[]
     */
    public function removedQuickFilters(): array
    {
        return $this->removedQuickFilters;
    }

    public function changeName(string $name): self
    {
        return $this->with(name: $name);
    }

    public function changeFilter(Filter $filter): self
    {
        return $this->with(filter: $filter);
    }

    public function removeFilter(): self
    {
        return $this->with(filter: null);
    }

    public function changeSorts(Sort ...$sorts): self
    {
        return $this->with(sorts: $sorts);
    }

    public function addSort(Sort $sort): self
    {
        return $this->with(sorts: [ ...$this->sorts, $sort ]);
    }

    /** Add a quick filter, replacing any existing quick filter on the same property */
    public function addQuickFilter(QuickFilter $quickFilter): self
    {
        $quickFilters = array_values(array_filter(
            $this->quickFilters,
            fn (QuickFilter $q) => $q->property !== $quickFilter->property,
        ));
        $quickFilters[] = $quickFilter;

        $removed = array_values(array_filter(
            $this->removedQuickFilters,
            fn (string $p) => $p !== $quickFilter->property,
        ));

        return $this->with(quickFilters: $quickFilters, removedQuickFilters: $removed);
    }

    /** @param string $property Property name or ID used as quick filter key */
    public function removeQuickFilter(string $property): self
    {
        $quickFilters = array_values(array_filter(
            $this->quickFilters,
            fn (QuickFilter $q) => $q->property !== $property,
        ));

        $removed = $this->removedQuickFilters;
        if (!in_array($property, $removed, true)) {
            $removed[] = $property;
        }

        return $this->with(quickFilters: $quickFilters, removedQuickFilters: $removed);
    }

    public function removeAllQuickFilters(): self
    {
        $removed = $this->removedQuickFilters;
        foreach ($this->quickFilters as $quickFilter) {
            if (!in_array($quickFilter->property, $removed, true)) {
                $removed[] = $quickFilter->property;
            }
        }

        return $this->with(quickFilters: [], removedQuickFilters: $removed);
    }

    /**
     * Change the type-specific configuration of the view.
     *
     * The configuration type must match the view type.
     */
    public function changeConfiguration(ViewConfiguration $configuration): self
    {
        if ($configuration instanceof DashboardConfiguration) {
            throw ViewException::readOnlyConfiguration();
        }

        if ($configuration->type() !== $this->type) {
            throw ViewException::configurationTypeMismatch($this->type->value, $configuration->type()->value);
        }

        return $this->with(configuration: $configuration);
    }

    /** @psalm-suppress MixedArgument */
    private function with(mixed ...$changes): self
    {
        return new self(...[ ...get_object_vars($this), ...$changes ]);
    }
}
