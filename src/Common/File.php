<?php

namespace Notion\Common;

use DateTimeImmutable;

/**
 * @psalm-import-type RichTextJson from \Notion\Common\RichText
 *
 * @psalm-type FileJson = array{
 *      type: "external"|"file"|"file_upload",
 *      file?: array{ url: string, expiry_time: string },
 *      external?: array{ url: string },
 *      file_upload?: array{ id: string },
 *      name?: string,
 *      caption?: list<RichTextJson>
 * }
 *
 * @psalm-immutable
 */
class File
{
    /** @param RichText[] $caption */
    private function __construct(
        public readonly FileType $type,
        public readonly string|null $url,
        public readonly string|null $fileId,
        public readonly DateTimeImmutable|null $expiryTime,
        public readonly string|null $name,
        public readonly array $caption,
    ) {
    }

    public static function createExternal(string $url): self
    {
        return new self(FileType::External, $url, null, null, null, []);
    }

    public static function createInternal(
        string $url,
        DateTimeImmutable|null $expiryTime = null
    ): self {
        return new self(FileType::Internal, $url, null, $expiryTime, null, []);
    }

    public static function createFileUpload(string $fileId): self
    {
        return new self(FileType::FileUpload, null, $fileId, null, null, []);
    }

    /**
     * @param FileJson $array
     *
     * @internal
     */
    public static function fromArray(array $array): self
    {
        $type = $array["type"];

        $file = $array[$type] ?? [];

        return new self(
            FileType::from($type),
            $file["url"] ?? "",
            $file["id"] ?? "",
            isset($file["expiry_time"]) ? new DateTimeImmutable($file["expiry_time"]) : null,
            $array["name"] ?? null,
            isset($array["caption"]) ? array_map(fn($c) => RichText::fromArray($c), $array["caption"]) : [],
        );
    }

    public function toArray(): array
    {
        $array = [];
        $type = $this->type;

        if ($type === FileType::Internal) {
            $array = [
                "type" => "file",
                "file" => [
                    "url" => $this->url,
                    "expiry_time" => $this->expiryTime?->format(Date::FORMAT),
                ],
            ];
        }

        if ($type === FileType::FileUpload) {
            $array = [
                "type" => "file_upload",
                "file_upload" => [ "id" => $this->fileId ],
            ];
        }

        if ($type === FileType::External) {
            $array = [
                "type" => "external",
                "external" => [ "url" => $this->url ],
            ];
        }

        if ($this->name !== null) {
            $array["name"] = $this->name;
        }

        if (count($this->caption) > 0) {
            $array["caption"] = array_map(fn($t) => $t->toArray(), $this->caption);
        }

        return $array;
    }

    public function isExternal(): bool
    {
        return $this->type === FileType::External;
    }

    public function isInternal(): bool
    {
        return $this->type === FileType::Internal;
    }

    public function isFileUpload(): bool
    {
        return $this->type === FileType::FileUpload;
    }

    public function changeUrl(string $url): self
    {
        return new self($this->type, $url, null, $this->expiryTime, $this->name, $this->caption);
    }

    public function changeFileUploadId(string $fileId): self
    {
        return new self($this->type, null, $fileId, $this->expiryTime, $this->name, $this->caption);
    }

    public function changeName(string $name): self
    {
        return new self($this->type, $this->url, $this->fileId, $this->expiryTime, $name, $this->caption);
    }

    public function changeCaption(RichText ...$caption): self
    {
        return new self($this->type, $this->url, $this->fileId, $this->expiryTime, $this->name, $caption);
    }
}
