<?php

namespace Notion\Pages\Properties;

use Notion\Common\Color;
use Notion\Databases\Properties\StatusOption;

/**
 * @psalm-type StatusJson = array{
 *      id: string,
 *      type: "status",
 *      status: array{ id: string, name: string, color: string }
 * }
 *
 * @psalm-immutable
 */
class Status implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly StatusOption $option,
    ) {
    }

    public static function fromId(string $id): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Status);
        $option = StatusOption::fromId($id);

        return new self($metadata, $option);
    }

    public static function fromName(string $name): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Status);
        $option = StatusOption::fromName($name);

        return new self($metadata, $option);
    }

    public function changeColor(Color $color): self
    {
        return new self($this->metadata, $this->option->changeColor($color));
    }


    public static function fromOption(StatusOption $option): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::Status);

        return new self($metadata, $option);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var StatusJson $array */
        $metadata = PropertyMetadata::fromArray($array);

        $option = StatusOption::fromArray($array["status"]);

        return new self($metadata, $option);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["status"] = $this->option->toArray();

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }
}
