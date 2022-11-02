<?php

namespace Notion\Databases\Properties;

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
 *          groups: StatusGroupJson[]
 *      },
 * }
 *
 * @psalm-immutable
 */
class Status implements PropertyInterface
{
    /**
     * @param StatusOption[] $options
     * @param StatusGroup[] $groups
     */
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly array $options,
        public readonly array $groups
    ) {
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
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

        return new self($property, $options, $groups);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["status"] = [
            "options" => array_map(fn(StatusOption $o) => $o->toArray(), $this->options),
            "groups" => array_map(fn(StatusGroup $g) => $g->toArray(), $this->groups),
        ];

        return $array;
    }
}
