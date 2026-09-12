<?php

namespace Notion\Pages\PropertyItems;

use Notion\Common\RichText;
use Notion\Pages\Properties\PropertyType;

/**
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type RichTextItemJson = array{
 *      id: string,
 *      type: "rich_text",
 *      rich_text: RichTextJson,
 *      ...
 * }
 *
 * @psalm-immutable
 */
final readonly class RichTextPropertyItem implements PropertyItemInterface
{
    private function __construct(
        private PropertyItemMetadata $metadata,
        public RichText $richText,
    ) {
    }

    public static function create(RichText $richText, string $id = ""): self
    {
        $metadata = PropertyItemMetadata::create($id, PropertyType::RichText);

        return new self($metadata, $richText);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RichTextItemJson $array */
        $metadata = PropertyItemMetadata::fromArray($array);
        $richText = RichText::fromArray($array["rich_text"]);

        return new self($metadata, $richText);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();
        $array["rich_text"] = $this->richText->toArray();

        return $array;
    }

    public function metadata(): PropertyItemMetadata
    {
        return $this->metadata;
    }
}
