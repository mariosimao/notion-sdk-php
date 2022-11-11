<?php

namespace Notion\Blocks;

use Notion\Exceptions\BlockException;
use Notion\Common\RichText;

/**
 * @psalm-import-type BlockMetadataJson from BlockMetadata
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type CodeJson = array{
 *      code: array{
 *          rich_text: RichTextJson[],
 *          caption: RichTextJson[],
 *          language: string,
 *      },
 * }
 *
 * @psalm-immutable
 */
class Code implements BlockInterface
{
    /**
     * @param RichText[] $text
     * @param RichText[] $caption
     */
    private function __construct(
        private readonly BlockMetadata $metadata,
        public readonly array $text,
        public readonly CodeLanguage $language,
        public readonly array $caption,
    ) {
        $metadata->checkType(BlockType::Code);
    }

    public static function create(): self
    {
        return self::fromText([]);
    }

    /** @param RichText[] $text */
    public static function fromText(
        array $text,
        CodeLanguage $language = CodeLanguage::PlainText,
    ): self {
        $metadata = BlockMetadata::create(BlockType::Code);

        return new self($metadata, $text, $language, []);
    }

    public static function fromString(
        string $code,
        CodeLanguage $language = CodeLanguage::PlainText,
    ): self {
        $metadata = BlockMetadata::create(BlockType::Code);
        $text = [ RichText::fromString($code) ];

        return new self($metadata, $text, $language, []);
    }

    public static function fromArray(array $array): self
    {
        /** @psalm-var BlockMetadataJson $array */
        $metadata = BlockMetadata::fromArray($array);

        /** @psalm-var CodeJson $array */
        $code = $array["code"];
        $text = array_map(fn($t) => RichText::fromArray($t), $code["rich_text"]);
        $caption = array_map(fn($t) => RichText::fromArray($t), $code["caption"]);
        $language = CodeLanguage::from($code["language"]);

        return new self($metadata, $text, $language, $caption);
    }

    public function toArray(): array
    {
        $array = $this->metadata->toArray();

        $array["code"] = [
            "rich_text" => array_map(fn(RichText $t) => $t->toArray(), $this->text),
            "caption"   => array_map(fn(RichText $t) => $t->toArray(), $this->caption),
            "language"  => $this->language->value,
        ];

        return $array;
    }

    public function toString(): string
    {
        return RichText::multipleToString(...$this->text);
    }

    public function metadata(): BlockMetadata
    {
        return $this->metadata;
    }

    public function changeText(RichText ...$text): self
    {
        return new self($this->metadata, $text, $this->language, $this->caption);
    }

    public function addText(RichText $text): self
    {
        $texts = $this->text;
        $texts[] = $text;

        return new self($this->metadata, $texts, $this->language, $this->caption);
    }

    public function changeLanguage(CodeLanguage $language): self
    {
        return new self($this->metadata, $this->text, $language, $this->caption);
    }

    public function changeCaption(RichText ...$caption): self
    {
        return new self($this->metadata, $this->text, $this->language, $caption);
    }

    public function addChild(BlockInterface $child): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function changeChildren(BlockInterface ...$children): never
    {
        throw BlockException::noChindrenSupport();
    }

    public function archive(): BlockInterface
    {
        return new self(
            $this->metadata->archive(),
            $this->text,
            $this->language,
            $this->caption,
        );
    }
}
