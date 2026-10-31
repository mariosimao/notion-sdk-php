<?php

namespace Notion\DataSources\Properties;

/**
 * @psalm-import-type StatusGroupJson from StatusGroup
 * @psalm-import-type StatusOptionJson from StatusOption
 *
 * @psalm-type StatusJson = array{
 *      id: string,
 *      name: string,
 *      type: "status",
 *      status: array{
 *          options: StatusOptionJson[],
 *          groups: StatusGroupJson[],
 *          default_option?: string|null,
 *      },
 *      description?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Status implements PropertyInterface
{
    /**
     * @param StatusOption[] $options
     * @param StatusGroup[] $groups
     */
    private function __construct(
        private PropertyMetadata $metadata,
        public array $options,
        public array $groups,
        public string|null $defaultOption = null,
    ) {
    }

    /**
     * Create a status property.
     *
     * Without options, Notion creates the default options (`Not started`,
     * `In progress` and `Done`) in the `To-do`, `In progress` and `Complete`
     * groups.
     *
     * @param StatusOption[] $options
     */
    public static function create(string $propertyName = "Status", array $options = []): self
    {
        $property = PropertyMetadata::create("", $propertyName, PropertyType::Status);

        return new self($property, $options, []);
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    /**
     * Replace the options of the property.
     *
     * Options omitted on an update are removed from the property.
     */
    public function changeOptions(StatusOption ...$options): self
    {
        return new self($this->metadata, $options, $this->groups, $this->defaultOption);
    }

    public function addOption(StatusOption $option): self
    {
        $options = $this->options;
        $options[] = $option;

        return new self($this->metadata, $options, $this->groups, $this->defaultOption);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var StatusJson $array */
        $property = PropertyMetadata::fromArray($array);
        $options = array_map(
            function (array $option): StatusOption {
                return StatusOption::fromArray($option);
            },
            $array["status"]["options"],
        );
        $groups = array_map(
            function (array $group): StatusGroup {
                return StatusGroup::fromArray($group);
            },
            $array["status"]["groups"],
        );
        $defaultOption = $array["status"]["default_option"] ?? null;

        return new self($property, $options, $groups, $defaultOption);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["status"] = [
            "options" => array_map(fn(StatusOption $o) => $o->toArray(), $this->options),
            "groups" => array_map(fn(StatusGroup $g) => $g->toArray(), $this->groups),
        ];

        if ($this->defaultOption !== null) {
            $array["status"]["default_option"] = $this->defaultOption;
        }

        return $array;
    }

    /**
     * Schema used on create and update requests.
     *
     * Groups and the default option are read-only and are not sent.
     *
     * @internal
     */
    public function toRequestArray(): array
    {
        $array = $this->metadata->toArray();
        $array["status"] = empty($this->options) ? new \stdClass() : [
            "options" => array_values(
                array_map(fn(StatusOption $o) => $o->toRequestArray(), $this->options),
            ),
        ];

        return $array;
    }
}
