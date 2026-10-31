<?php

namespace Notion\Common;

use Notion\Exceptions\IconException;

/**
 * @psalm-import-type EmojiJson from Emoji
 * @psalm-import-type FileJson from File
 * @psalm-import-type NativeIconJson from NativeIcon
 *
 * @psalm-type IconJson = EmojiJson|FileJson|NativeIconJson
 */
final readonly class Icon
{
    private function __construct(
        public Emoji|null $emoji,
        public File|null $file,
        public NativeIcon|null $nativeIcon = null,
    ) {
        $count = count(array_filter([$emoji, $file, $nativeIcon], fn($v) => $v !== null));

        if ($count === 0) {
            throw IconException::bothNull();
        }

        if ($count > 1) {
            throw IconException::bothSet();
        }
    }

    /** @psalm-mutation-free */
    public static function fromEmoji(Emoji $emoji): self
    {
        return new self($emoji, null);
    }

    /** @psalm-mutation-free */
    public static function fromFile(File $file): self
    {
        return new self(null, $file);
    }

    /** @psalm-mutation-free */
    public static function fromNativeIcon(NativeIcon $nativeIcon): self
    {
        return new self(null, null, $nativeIcon);
    }

    /** @psalm-mutation-free */
    public static function from(Emoji|File|NativeIcon|self $icon): self
    {
        return match (true) {
            $icon instanceof Emoji      => self::fromEmoji($icon),
            $icon instanceof File       => self::fromFile($icon),
            $icon instanceof NativeIcon => self::fromNativeIcon($icon),
            default                     => $icon,
        };
    }

    /**
     * @param array{ type: string, ... } $array
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
     * @param array{ type: string, ... } $array
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

        if ($type === "file" || $type === "external") {
            /** @psalm-var FileJson $array */
            return self::fromFile(File::fromArray($array));
        }

        if ($type === "icon") {
            /** @psalm-var NativeIconJson $array */
            return self::fromNativeIcon(NativeIcon::fromArray($array));
        }

        return null;
    }

    /** @psalm-mutation-free */
    public function toArray(): array
    {
        if ($this->emoji !== null) {
            return $this->emoji->toArray();
        }

        if ($this->file !== null) {
            return $this->file->toArray();
        }

        if ($this->nativeIcon !== null) {
            return $this->nativeIcon->toArray();
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
     * @psalm-assert-if-true NativeIcon $this->nativeIcon
     */
    public function isNativeIcon(): bool
    {
        return $this->nativeIcon !== null;
    }
}
