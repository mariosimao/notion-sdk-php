<?php

namespace Notion\Views\Configuration;

use Notion\Exceptions\ViewException;

/**
 * Grouping of view rows, cards or chart axes by a property
 *
 * Formula group-bys contain a nested group-by describing how to group the
 * formula result (see `GroupBy::formula()` and the `formula*Result()`
 * constructors).
 *
 * @psalm-type GroupByJson = array{
 *      type: string,
 *      property_id?: string,
 *      property_name?: string,
 *      group_by?: string|array,
 *      sort?: array{ type: string },
 *      hide_empty_groups?: bool,
 *      start_day_of_week?: int,
 *      range_start?: int,
 *      range_end?: int,
 *      range_size?: int,
 * }
 *
 * @psalm-immutable
 */
final readonly class GroupBy
{
    private const DATE_TYPES = [ GroupByType::Date, GroupByType::CreatedTime, GroupByType::LastEditedTime ];
    private const TEXT_TYPES = [
        GroupByType::Text,
        GroupByType::Title,
        GroupByType::Url,
        GroupByType::Email,
        GroupByType::PhoneNumber,
    ];

    private function __construct(
        public GroupByType $type,
        public string|null $propertyId,
        public string|null $propertyName,
        public StatusGroupBy|DateGroupBy|TextGroupBy|GroupBy|null $groupBy,
        public GroupSort|null $sort,
        public bool|null $hideEmptyGroups,
        public int|null $startDayOfWeek,
        public int|null $rangeStart,
        public int|null $rangeEnd,
        public int|null $rangeSize,
    ) {
    }

    private static function simple(
        GroupByType $type,
        string|null $propertyId,
        StatusGroupBy|DateGroupBy|TextGroupBy|GroupBy|null $groupBy = null,
        GroupSort|null $sort = GroupSort::Manual,
    ): self {
        return new self($type, $propertyId, null, $groupBy, $sort, null, null, null, null, null);
    }

    public static function select(string $propertyId): self
    {
        return self::simple(GroupByType::Select, $propertyId);
    }

    public static function multiSelect(string $propertyId): self
    {
        return self::simple(GroupByType::MultiSelect, $propertyId);
    }

    public static function status(string $propertyId, StatusGroupBy $groupBy = StatusGroupBy::Group): self
    {
        return self::simple(GroupByType::Status, $propertyId, $groupBy);
    }

    public static function person(string $propertyId): self
    {
        return self::simple(GroupByType::Person, $propertyId);
    }

    public static function createdBy(string $propertyId): self
    {
        return self::simple(GroupByType::CreatedBy, $propertyId);
    }

    public static function lastEditedBy(string $propertyId): self
    {
        return self::simple(GroupByType::LastEditedBy, $propertyId);
    }

    public static function relation(string $propertyId): self
    {
        return self::simple(GroupByType::Relation, $propertyId);
    }

    public static function date(string $propertyId, DateGroupBy $groupBy): self
    {
        return self::simple(GroupByType::Date, $propertyId, $groupBy);
    }

    public static function createdTime(string $propertyId, DateGroupBy $groupBy): self
    {
        return self::simple(GroupByType::CreatedTime, $propertyId, $groupBy);
    }

    public static function lastEditedTime(string $propertyId, DateGroupBy $groupBy): self
    {
        return self::simple(GroupByType::LastEditedTime, $propertyId, $groupBy);
    }

    public static function text(string $propertyId, TextGroupBy $groupBy = TextGroupBy::Exact): self
    {
        return self::simple(GroupByType::Text, $propertyId, $groupBy);
    }

    public static function title(string $propertyId, TextGroupBy $groupBy = TextGroupBy::Exact): self
    {
        return self::simple(GroupByType::Title, $propertyId, $groupBy);
    }

    public static function url(string $propertyId, TextGroupBy $groupBy = TextGroupBy::Exact): self
    {
        return self::simple(GroupByType::Url, $propertyId, $groupBy);
    }

    public static function email(string $propertyId, TextGroupBy $groupBy = TextGroupBy::Exact): self
    {
        return self::simple(GroupByType::Email, $propertyId, $groupBy);
    }

    public static function phoneNumber(string $propertyId, TextGroupBy $groupBy = TextGroupBy::Exact): self
    {
        return self::simple(GroupByType::PhoneNumber, $propertyId, $groupBy);
    }

    public static function number(string $propertyId): self
    {
        return self::simple(GroupByType::Number, $propertyId);
    }

    public static function checkbox(string $propertyId): self
    {
        return self::simple(GroupByType::Checkbox, $propertyId);
    }

    /**
     * @param GroupBy $result How to group the formula result. Use one of
     *                        `formulaDateResult()`, `formulaTextResult()`,
     *                        `formulaNumberResult()` or `formulaCheckboxResult()`.
     */
    public static function formula(string $propertyId, GroupBy $result): self
    {
        return self::simple(GroupByType::Formula, $propertyId, $result, null);
    }

    public static function formulaDateResult(DateGroupBy $groupBy): self
    {
        return self::simple(GroupByType::Date, null, $groupBy);
    }

    public static function formulaTextResult(TextGroupBy $groupBy = TextGroupBy::Exact): self
    {
        return self::simple(GroupByType::Text, null, $groupBy);
    }

    public static function formulaNumberResult(): self
    {
        return self::simple(GroupByType::Number, null);
    }

    public static function formulaCheckboxResult(): self
    {
        return self::simple(GroupByType::Checkbox, null);
    }

    /**
     * @param GroupByJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = GroupByType::from($array["type"]);

        $groupBy = null;
        if (isset($array["group_by"])) {
            $rawGroupBy = $array["group_by"];
            if (is_array($rawGroupBy)) {
                /** @psalm-var GroupByJson $rawGroupBy */
                $groupBy = self::fromArray($rawGroupBy);
            } elseif ($type === GroupByType::Status) {
                $groupBy = StatusGroupBy::from($rawGroupBy);
            } elseif (in_array($type, self::DATE_TYPES, true)) {
                $groupBy = DateGroupBy::from($rawGroupBy);
            } elseif (in_array($type, self::TEXT_TYPES, true)) {
                $groupBy = TextGroupBy::from($rawGroupBy);
            }
        }

        return new self(
            $type,
            $array["property_id"] ?? null,
            $array["property_name"] ?? null,
            $groupBy,
            isset($array["sort"]) ? GroupSort::from($array["sort"]["type"]) : null,
            $array["hide_empty_groups"] ?? null,
            $array["start_day_of_week"] ?? null,
            $array["range_start"] ?? null,
            $array["range_end"] ?? null,
            $array["range_size"] ?? null,
        );
    }

    public function toArray(): array
    {
        $array = [ "type" => $this->type->value ];

        if ($this->propertyId !== null) {
            $array["property_id"] = $this->propertyId;
        }
        if ($this->groupBy instanceof GroupBy) {
            $array["group_by"] = $this->groupBy->toArray();
        } elseif ($this->groupBy !== null) {
            $array["group_by"] = $this->groupBy->value;
        }
        if ($this->sort !== null) {
            $array["sort"] = [ "type" => $this->sort->value ];
        }
        if ($this->hideEmptyGroups !== null) {
            $array["hide_empty_groups"] = $this->hideEmptyGroups;
        }
        if ($this->startDayOfWeek !== null) {
            $array["start_day_of_week"] = $this->startDayOfWeek;
        }
        if ($this->rangeStart !== null) {
            $array["range_start"] = $this->rangeStart;
        }
        if ($this->rangeEnd !== null) {
            $array["range_end"] = $this->rangeEnd;
        }
        if ($this->rangeSize !== null) {
            $array["range_size"] = $this->rangeSize;
        }

        return $array;
    }

    public function changeSort(GroupSort $sort): self
    {
        return new self(
            $this->type,
            $this->propertyId,
            $this->propertyName,
            $this->groupBy,
            $sort,
            $this->hideEmptyGroups,
            $this->startDayOfWeek,
            $this->rangeStart,
            $this->rangeEnd,
            $this->rangeSize,
        );
    }

    public function hideEmptyGroups(): self
    {
        return $this->changeHideEmptyGroups(true);
    }

    public function showEmptyGroups(): self
    {
        return $this->changeHideEmptyGroups(false);
    }

    private function changeHideEmptyGroups(bool $hide): self
    {
        return new self(
            $this->type,
            $this->propertyId,
            $this->propertyName,
            $this->groupBy,
            $this->sort,
            $hide,
            $this->startDayOfWeek,
            $this->rangeStart,
            $this->rangeEnd,
            $this->rangeSize,
        );
    }

    /**
     * Only available for date group-bys
     *
     * @param int $startDayOfWeek 0 (Sunday) or 1 (Monday)
     */
    public function changeStartDayOfWeek(int $startDayOfWeek): self
    {
        if (!($this->groupBy instanceof DateGroupBy)) {
            throw ViewException::unsupportedGroupByOption($this->type->value);
        }

        if ($startDayOfWeek !== 0 && $startDayOfWeek !== 1) {
            throw ViewException::invalidStartDayOfWeek();
        }

        return new self(
            $this->type,
            $this->propertyId,
            $this->propertyName,
            $this->groupBy,
            $this->sort,
            $this->hideEmptyGroups,
            $startDayOfWeek,
            $this->rangeStart,
            $this->rangeEnd,
            $this->rangeSize,
        );
    }

    /** Group numbers into buckets. Only available for number group-bys. */
    public function changeNumberRange(int $start, int $end, int $size): self
    {
        if ($this->type !== GroupByType::Number) {
            throw ViewException::unsupportedGroupByOption($this->type->value);
        }

        return new self(
            $this->type,
            $this->propertyId,
            $this->propertyName,
            $this->groupBy,
            $this->sort,
            $this->hideEmptyGroups,
            $this->startDayOfWeek,
            $start,
            $end,
            $size,
        );
    }
}
