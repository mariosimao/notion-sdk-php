<?php

namespace Notion\Common;

use DateTimeImmutable;

/**
 * @psalm-type FileJson = array{
 *      type: "external"|"file",
 *      name: string,
 *      file?: array{ url: string, expiry_time: string },
 *      external?: array{ url: string },
 * }
 *
 * @psalm-immutable
 */
class File
{
    private function __construct(
        public readonly FileType $type,
        public readonly string $url,
        public readonly DateTimeImmutable|null $expiryTime,
        public readonly string $name,
    ) {
    }

    public static function createExternal(string $url): self
    {
        return new self(FileType::External, $url, null, "File");
    }

    public static function createInternal(
        string $url,
        DateTimeImmutable|null $expiryTime = null
    ): self {
        return new self(FileType::Internal, $url, $expiryTime, "File");
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
            isset($file["expiry_time"]) ? new DateTimeImmutable($file["expiry_time"]) : null,
            $array["name"] ?? "File",
        );
    }

    public function toArray(): array
    {
        $array = [];
        $type = $this->type;

        if ($type === FileType::Internal) {
            $array = [
                "type" => "file",
                "name" => $this->name,
                "file" => [
                    "url" => $this->url,
                    "expiry_time" => $this->expiryTime?->format(Date::FORMAT),
                ],
            ];
        }

        if ($type === FileType::External) {
            $array = [
                "type" => "external",
                "name" => $this->name,
                "external" => [ "url" => $this->url ],
            ];
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

    public function changeUrl(string $url): self
    {
        return new self($this->type, $url, $this->expiryTime, $this->name);
    }

    public function changeName(string $name): self
    {
        return new self($this->type, $this->url, $this->expiryTime, $name);
    }
}
