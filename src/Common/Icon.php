<?php

namespace Notion\Common;

use Notion\Exceptions\IconException;

/**
 * @psalm-import-type EmojiJson from Emoji
 * @psalm-import-type CustomEmojiJson from CustomEmoji
 * @psalm-import-type FileJson from File
 *
 * @psalm-type IconJson = EmojiJson|CustomEmojiJson|FileJson
 *
 * @psalm-immutable
 */
final readonly class Icon
{
    private function __construct(
        public Emoji|null $emoji,
        public File|null $file,
        public CustomEmoji|null $customEmoji,
    ) {
        $setCount = count(array_filter(
            [$emoji, $file, $customEmoji],
            fn($value) => $value !== null,
        ));

        if ($setCount === 0) {
            throw IconException::noneSet();
        }

        if ($setCount > 1) {
            throw IconException::multipleSet();
        }
    }

    /** @psalm-mutation-free */
    public static function fromEmoji(Emoji $emoji): self
    {
        return new self($emoji, null, null);
    }

    /** @psalm-mutation-free */
    public static function fromFile(File $file): self
    {
        return new self(null, $file, null);
    }

    /** @psalm-mutation-free */
    public static function fromCustomEmoji(CustomEmoji $customEmoji): self
    {
        return new self(null, null, $customEmoji);
    }

    /**
     * Create an icon from an emoji, custom emoji, file or another icon.
     *
     * @psalm-mutation-free
     */
    public static function from(Emoji|CustomEmoji|File|self $icon): self
    {
        if ($icon instanceof Emoji) {
            return self::fromEmoji($icon);
        }

        if ($icon instanceof CustomEmoji) {
            return self::fromCustomEmoji($icon);
        }

        if ($icon instanceof File) {
            return self::fromFile($icon);
        }

        return $icon;
    }

    /**
     * @param IconJson $array
     *
     * @throws IconException When the icon type is not supported.
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $icon = self::tryFromArray($array);

        if ($icon === null) {
            throw IconException::unsupportedType($array["type"]);
        }

        return $icon;
    }

    /**
     * Same as `fromArray()`, but returns `null` for unsupported icon types.
     *
     * @param IconJson|array{ type: string } $array
     *
     * @internal
     */
    public static function tryFromArray(array $array): self|null
    {
        $type = $array["type"];

        if ($type === "emoji") {
            /** @psalm-var EmojiJson $array */
            return self::fromEmoji(Emoji::fromArray($array));
        }

        if ($type === "custom_emoji") {
            /** @psalm-var CustomEmojiJson $array */
            return self::fromCustomEmoji(CustomEmoji::fromArray($array));
        }

        if ($type === "file" || $type === "external" || $type === "file_upload") {
            /** @psalm-var FileJson $array */
            return self::fromFile(File::fromArray($array));
        }

        return null;
    }

    public function toArray(): array
    {
        if ($this->emoji !== null) {
            return $this->emoji->toArray();
        }

        if ($this->customEmoji !== null) {
            return $this->customEmoji->toArray();
        }

        if ($this->file !== null) {
            return $this->file->toArray();
        }

        return [];
    }

    /**
     * @psalm-assert-if-true Emoji $this->emoji
     */
    public function isEmoji(): bool
    {
        return $this->emoji !== null;
    }

    /**
     * @psalm-assert-if-true File $this->file
     */
    public function isFile(): bool
    {
        return $this->file !== null;
    }

    /**
     * @psalm-assert-if-true CustomEmoji $this->customEmoji
     */
    public function isCustomEmoji(): bool
    {
        return $this->customEmoji !== null;
    }
}
