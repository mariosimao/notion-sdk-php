<?php

namespace Notion\Sessions\Events;

use DateTimeImmutable;
use Notion\Common\Date;

/**
 * A piece of text or a file sent in a message.
 *
 * @psalm-type ContentJson = array{
 *      type: "text"|"file",
 *      text?: string,
 *      name?: string,
 *      content_type?: string,
 *      url?: string,
 *      expiry_time?: string,
 *      file_id?: string,
 * }
 *
 * @psalm-immutable
 */
final readonly class Content
{
    private function __construct(
        public ContentType $type,
        public string|null $text,
        public string|null $name,
        public string|null $contentType,
        public string|null $url,
        public DateTimeImmutable|null $expiryTime,
        public string|null $fileId,
    ) {
    }

    /**
     * @param ContentJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $expiryTime = $array["expiry_time"] ?? null;

        return new self(
            ContentType::from($array["type"]),
            $array["text"] ?? null,
            $array["name"] ?? null,
            $array["content_type"] ?? null,
            $array["url"] ?? null,
            $expiryTime !== null ? new DateTimeImmutable($expiryTime) : null,
            $array["file_id"] ?? null,
        );
    }

    /**
     * @param list<ContentJson> $array
     *
     * @return list<self>
     *
     * @internal
     */
    public static function fromArrayList(array $array): array
    {
        return array_map(self::fromArray(...), $array);
    }

    /**
     * Concatenate the text of multiple content items, ignoring files.
     *
     * @param Content[] $contents
     *
     * @psalm-pure
     */
    public static function multipleToString(array $contents): string
    {
        return implode("", array_map(fn (Content $c) => $c->text ?? "", $contents));
    }

    /** @return ContentJson */
    public function toArray(): array
    {
        $array = [ "type" => $this->type->value ];

        if ($this->text !== null) {
            $array["text"] = $this->text;
        }
        if ($this->name !== null) {
            $array["name"] = $this->name;
        }
        if ($this->contentType !== null) {
            $array["content_type"] = $this->contentType;
        }
        if ($this->url !== null) {
            $array["url"] = $this->url;
        }
        if ($this->expiryTime !== null) {
            $array["expiry_time"] = $this->expiryTime->format(Date::FORMAT);
        }
        if ($this->fileId !== null) {
            $array["file_id"] = $this->fileId;
        }

        return $array;
    }
}
