<?php

namespace Notion\Views\Configuration;

use Notion\Views\ViewType;

/**
 * @psalm-import-type PropertyConfigurationJson from PropertyConfiguration
 * @psalm-import-type GroupByJson from GroupBy
 * @psalm-import-type SubtaskConfigurationJson from SubtaskConfiguration
 *
 * @psalm-type TableConfigurationJson = array{
 *      type: "table",
 *      properties?: PropertyConfigurationJson[]|null,
 *      group_by?: GroupByJson|null,
 *      subtasks?: SubtaskConfigurationJson|null,
 *      wrap_cells?: bool,
 *      frozen_column_index?: int,
 *      show_vertical_lines?: bool,
 * }
 *
 * @psalm-immutable
 */
final readonly class TableConfiguration implements ViewConfiguration
{
    /** @param PropertyConfiguration[] $properties */
    private function __construct(
        public array $properties,
        public GroupBy|null $groupBy,
        public SubtaskConfiguration|null $subtasks,
        public bool|null $wrapCells,
        public int|null $frozenColumnIndex,
        public bool|null $showVerticalLines,
    ) {
    }

    public static function create(): self
    {
        return new self([], null, null, null, null, null);
    }

    /**
     * @param TableConfigurationJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        return new self(
            array_map(PropertyConfiguration::fromArray(...), $array["properties"] ?? []),
            isset($array["group_by"]) ? GroupBy::fromArray($array["group_by"]) : null,
            isset($array["subtasks"]) ? SubtaskConfiguration::fromArray($array["subtasks"]) : null,
            $array["wrap_cells"] ?? null,
            $array["frozen_column_index"] ?? null,
            $array["show_vertical_lines"] ?? null,
        );
    }

    public function type(): ViewType
    {
        return ViewType::Table;
    }

    public function toArray(): array
    {
        $array = [
            "type"       => $this->type()->value,
            "properties" => PropertyConfigurations::toArray($this->properties),
            "group_by"   => $this->groupBy?->toArray(),
            "subtasks"   => $this->subtasks?->toArray(),
        ];

        if ($this->wrapCells !== null) {
            $array["wrap_cells"] = $this->wrapCells;
        }
        if ($this->frozenColumnIndex !== null) {
            $array["frozen_column_index"] = $this->frozenColumnIndex;
        }
        if ($this->showVerticalLines !== null) {
            $array["show_vertical_lines"] = $this->showVerticalLines;
        }

        return $array;
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

    public function changeGroupBy(GroupBy $groupBy): self
    {
        return $this->with(groupBy: $groupBy);
    }

    public function removeGroupBy(): self
    {
        return $this->with(groupBy: null);
    }

    public function changeSubtasks(SubtaskConfiguration $subtasks): self
    {
        return $this->with(subtasks: $subtasks);
    }

    /** Reset sub-item settings to defaults. Use `SubtaskConfiguration::disabled()` to disable them. */
    public function removeSubtasks(): self
    {
        return $this->with(subtasks: null);
    }

    public function changeWrapCells(bool $wrapCells): self
    {
        return $this->with(wrapCells: $wrapCells);
    }

    public function changeFrozenColumnIndex(int $frozenColumnIndex): self
    {
        return $this->with(frozenColumnIndex: $frozenColumnIndex);
    }

    public function changeShowVerticalLines(bool $showVerticalLines): self
    {
        return $this->with(showVerticalLines: $showVerticalLines);
    }

    /** @psalm-suppress MixedArgument */
    private function with(mixed ...$changes): self
    {
        return new self(...[ ...get_object_vars($this), ...$changes ]);
    }
}
