<?php

namespace Notion\Common;

use DateTimeImmutable;

/**
 * @psalm-type FileJson = array{
 *      type: "external"|"file",
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
    ) {
    }

    public static function createExternal(string $url): self
    {
        return new self(FileType::External, $url, null);
    }

    public static function createInternal(
        string $url,
        DateTimeImmutable|null $expiryTime = null
    ): self {
        return new self(FileType::Internal, $url, $expiryTime);
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

        if ($type === FileType::External) {
            $array = [
                "type" => "external",
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
        return new self($this->type, $url, $this->expiryTime);
    }
}
