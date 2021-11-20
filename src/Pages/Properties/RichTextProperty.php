<?php

namespace Notion\Pages\Properties;

use Notion\Common\RichText;

/**
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type RichTextPropertyJson = array{
 *      id: string,
 *      type: "rich_text",
 *      rich_text: list<RichTextJson>,
 * }
 *
 * @psalm-immutable
 */
class RichTextProperty implements PropertyInterface
{
    private const TYPE = Property::TYPE_RICH_TEXT;

    private Property $property;

    /** @var list<RichText> */
    private array $text;

    /** @param list<RichText> $text */
    private function __construct(Property $property, array $text)
    {
        $this->property = $property;
        $this->text = $text;
    }

    public static function create(string $text): self
    {
        $property = Property::create("", self::TYPE);
        $richText = [ RichText::createText($text) ];

        return new self($property, $richText);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var RichTextPropertyJson $array */

        $property = Property::fromArray($array);

        $text = array_map(
            function (array $richTextArray): RichText {
                return RichText::fromArray($richTextArray);
            },
            $array[self::TYPE],
        );

        return new self($property, $text);
    }

    public function toArray(): array
    {
        $array = $this->property->toArray();

        $array[self::TYPE] = array_map(fn(RichText $richText) => $richText->toArray(), $this->text);

        return $array;
    }

    public function property(): Property
    {
        return $this->property;
    }

    /** @return list<RichText> */
    public function text(): array
    {
        return $this->text;
    }

    /** @param list<RichText> $text */
    public function withText(array $text): self
    {
        return new self($this->property, $text);
    }

    public function toString(): string
    {
        $string = "";
        foreach ($this->text as $textPart) {
            $string = $string . $textPart->plainText();
        }

        return $string;
    }
}
