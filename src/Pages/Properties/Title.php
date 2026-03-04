<?php

namespace Notion\Pages\Properties;

use Notion\Common\RichText;

use function PHPUnit\Framework\isEmpty;

/**
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type TitleJson = array{
 *      id: "title",
 *      type: "title",
 *      title: list<RichTextJson>,
 * }
 *
 * @psalm-immutable
 */
class Title implements PropertyInterface
{
    /** @param RichText[] $title */
    private function __construct(
        private readonly PropertyMetadata $metadata,
        public readonly array $title
    ) {
    }

    public static function createEmpty(): self
    {
        $property = PropertyMetadata::create("title", PropertyType::Title);

        return new self($property, []);
    }

    /** @psalm-mutation-free */
    public static function fromText(RichText ...$title): self
    {
        $property = PropertyMetadata::create("title", PropertyType::Title);

        return new self($property, $title);
    }

    /** @psalm-mutation-free */
    public static function fromString(string $title): self
    {
        $title = RichText::fromString($title);

        return self::fromText($title);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var TitleJson $array */

        $property = PropertyMetadata::fromArray($array);

        $title = array_map(
            function (array $richTextArray): RichText {
                return RichText::fromArray($richTextArray);
            },
            $array["title"],
        );

        return new self($property, $title);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["title"] = array_map(fn(RichText $richText) => $richText->toArray(), $this->title);

        return $array;
    }

    public function metadata(): PropertyMetadata
    {
        return $this->metadata;
    }

    public function change(RichText ...$title): self
    {
        return new self($this->metadata, $title);
    }

    public function isEmpty(): bool
    {
        $title = RichText::multipleToString(...$this->title);

        return strlen(trim($title)) === 0;
    }

    public function toString(): string
    {
        return RichText::multipleToString(...$this->title);
    }
}
