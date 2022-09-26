<?php

namespace Notion\Pages\Properties;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * @psalm-type LastEditedTimeJson = array{
 *      id: string,
 *      type: "last_edited_time",
 *      last_edited_time: string,
 * }
 *
 * @psalm-immutable
 */
class LastEditedTime implements PropertyInterface
{
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly DateTimeImmutable $time,
    ) {
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var LastEditedTimeJson $array */

        $metadata = PropertyMetadata::fromArray($array);

        $time = new DateTimeImmutable($array["last_edited_time"]);

        return new self($metadata, $time);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["last_edited_time"] = $this->time->format(Date::FORMAT);

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }
}
