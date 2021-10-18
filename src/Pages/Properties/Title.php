<?php

namespace Notion\Pages\Properties;

use Notion\Common\RichText;

class Title
{
    private string $id = "title";
    private string $type = "title";

    /** @var \Notion\Common\RichText[] */
    private array $title;

    private function __construct(RichText ...$title)
    {
        $this->title = $title;
    }

    public static function create(string $title): self
    {
        $richText = RichText::createText($title);

        return new self($richText);
    }

    public static function fromArray(array $array)
    {
        if ($array["type"] !== "title") {
            throw new \Exception("Not valid title type. Title properties should have type 'title'.");
        }

        if (isset($array["title"]["type"])) {
            $array["title"] = [ $array["title"] ];
        }

        $title = array_map(
            function (array $richTextArray): RichText {
                return RichText::fromArray($richTextArray);
            },
            $array["title"],
        );

        return new self(...$title);
    }

    public function toArray(): array
    {
        return [
            "id"    => $this->id,
            "type"  => $this->type,
            "title" => array_map(fn(RichText $richText) => $richText->toArray(), $this->title),
        ];
    }

    /** @return RichText[] */
    public function richTexts(): array
    {
        return $this->title;
    }

    public function withRichTexts(RichText ...$richTexts): self
    {
        return new self(...$richTexts);
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
