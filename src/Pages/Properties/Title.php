<?php

namespace Notion\Pages\Properties;

use Notion\Common\RichText;

/**
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type TitleJson = array{
 *      id: "title",
 *      type: "title",
 *      title: RichTextJson[],
 * }
 */
class Title implements PropertyInterface
{
    private Property $property;

    /** @var \Notion\Common\RichText[] */
    private array $title;


    private function __construct(Property $property, RichText ...$title)
    {
        $this->property = $property;
        $this->title = $title;
    }

    public static function create(string $title): self
    {
        $property = Property::create("title", "title");
        $richText = RichText::createText($title);

        return new self($property, $richText);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var TitleJson $array */

        $property = Property::fromArray($array);

        $title = array_map(
            function (array $richTextArray): RichText {
                return RichText::fromArray($richTextArray);
            },
            $array["title"],
        );

        return new self($property, ...$title);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array["title"] = array_map(fn(RichText $richText) => $richText->toArray(), $this->title);

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    /** @return RichText[] */
    public function richTexts(): array
    {
        return $this->title;
    }

    public function withRichTexts(RichText ...$richTexts): self
    {
        return new self($this->property, ...$richTexts);
    }

    public function toString(): string
    {
        $string = "";
        foreach ($this->title as $richText) {
            $string = $string . $richText->plainText();
        }

        return $string;
    }
}
