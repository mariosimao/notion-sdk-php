<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\RichText;
use Notion\NotionException;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type CodeJson = array{
 *      code: array{
 *          rich_text: list<RichTextJson>,
 *          language: string,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Code implements BlockInterface
{
    /** @param RichText[] $text */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $text,
        public readonly CodeLanguage $language,
    ) {
        $metadata->checkType(BlockType::Code);
    }

    /** @param RichText[] $text */
    public static function create(
        array $text = [],
        CodeLanguage $language = CodeLanguage::PlainText,
    ): self {
        $metadata = BlockMetadata::create(BlockType::Code);

        return new self($metadata, $text, $language);
    }

    public static function createFromString(
        string $code,
        CodeLanguage $language = CodeLanguage::PlainText,
    ): self {
        $metadata = BlockMetadata::create(BlockType::Code);
        $text = [ RichText::createText($code) ];

        return new self($metadata, $text, $language);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var CodeJson $array */
        $code = $array["code"];
        $text = array_map(fn($t) => RichText::fromArray($t), $code["rich_text"]);
        $language = CodeLanguage::from($code["language"]);

        return new self($metadata, $text, $language);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["code"] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "language"  => $this->language->value,
        ];

        return $array;
    }

    public function toString(): string
    {
        return RichText::multipleToString(...$this->text);
    }

    /** @internal */
    public function toUpdateArray(): array
    {
        return [
            "code" => [
                "rich_text"     => array_map(fn(RichText $t) => $t->toArray(), $this->text),
                "language" => $this->language,
            ],
            "archived" => $this->metadata()->archived,
        ];
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function changeText(RichText ...$text): self
    {
        return new self($this->metadata, $text, $this->language);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->language);
    }

    public function changeLanguage(CodeLanguage $language): self
    {
        return new self($this->metadata, $this->text, $language);
    }

    public function addChild(BlockInterface $child): self
    {
        throw BlockException::noChindrenSupport();
    }

    public function changeChildren(BlockInterface ...$children): self
    {
        throw BlockException::noChindrenSupport();
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->text,
            $this->language,
        );
    }
}
