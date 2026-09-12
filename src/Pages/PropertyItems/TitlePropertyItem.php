<?php

namespace Notion\Pages\PropertyItems;

use Notion\Common\RichText;
use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type TitleItemJson = array{
 *      id: string,
 *      type: "title",
 *      title: RichTextJson,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class TitlePropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public RichText $title,
    ) {
    }

    public static function create(RichText $title, string $id = "title"): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::Title);

        return new self($metadata, $title);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var TitleItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $title = RichText::fromArray($array["title"]);

        return new self($metadata, $title);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["title"] = $this->title->toArray();

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }
}
