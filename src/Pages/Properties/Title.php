<?php

namespace Notion\Pages\Properties;

use Notion\Common\RichText;

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
    private Property $property;

    /** @var list<RichText> */
    private array $title;


    /** @param list<RichText> $title */
    private function __construct(Property $property, array $title)
    {
        $this->property = $property;
        $this->title = $title;
    }

    /** @psalm-mutation-free */
    public static function create(string $title): self
    {
        $property = Property::create("title", "title");
        $richText = [ RichText::createText($title) ];

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

        return new self($property, $title);
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

    /** @return list<RichText> */
    public function richTexts(): array
    {
        return $this->title;
    }

    /** @param list<RichText> $richTexts */
    public function withRichTexts(array $richTexts): self
    {
        return new self($this->property, $richTexts);
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
