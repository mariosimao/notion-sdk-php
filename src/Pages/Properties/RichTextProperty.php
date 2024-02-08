<?php

namespace Notion\Pages\Properties;

use Notion\Common\RichText;

/**
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type RichTextPropertyMetadataJson = array{
 *      id: string,
 *      type: "rich_text",
 *      rich_text: list<RichTextJson>,
 * }
 *
 * @psalm-immutable
 */
class RichTextProperty implements PropertyInterface
{
    /** @param RichText[] $text */
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly array $text
    ) {
    }

    public static function fromText(RichText ...$texts): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::RichText);

        return new self($metadata, $texts);
    }

    public static function fromString(string $text): self
    {
        $metadata = PropertyMetadata::create("", PropertyType::RichText);
        $texts = [ RichText::fromString($text) ];

        return new self($metadata, $texts);
    }

    public static function createEmpty(string $id = null): self
    {
        $metadata = PropertyMetadata::create($id ?? "", PropertyType::RichText);

        return new self($metadata, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RichTextPropertyMetadataJson $array */

        $metadata = PropertyMetadata::fromArray($array);

        $text = array_map(
            function (array $richTextArray): RichText {
                return RichText::fromArray($richTextArray);
            },
            $array["rich_text"],
        );

        return new self($metadata, $text);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["rich_text"] = array_map(fn(RichText $richText) => $richText->toArray(), $this->text);

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function changeText(RichText ...$texts): self
    {
        return new self($this->metadata, $texts);
    }

    public function clear(): self
    {
        return new self($this->metadata, []);
    }

    public function isEmpty(): bool
    {
        return count($this->text) === 0;
    }

    public function toString(): string
    {
        $string = "";
        foreach ($this->text as $textPart) {
            $string = $string . $textPart->plainText;
        }

        return $string;
    }
}
