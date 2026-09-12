<?php

namespace Notion\Pages\PropertyItems;

use Notion\DataSources\Properties\StatusOption;
use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-type StatusItemJson = array{
 *      id: string,
 *      type: "status",
 *      status: array{ id: string, name: string, color: string }|null,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class StatusPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public StatusOption|null $option,
    ) {
    }

    public static function create(StatusOption|null $option, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Status);

        return new self($metadata, $option);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var StatusItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $option = $array["status"] ? StatusOption::fromArray($array["status"]) : null;

        return new self($metadata, $option);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["status"] = $this->option?->toArray();

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }

    public function isEmpty(): bool
    {
        return $this->option === null;
    }
}
