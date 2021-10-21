<?php

namespace Notion\Common;

use DateTimeImmutable;

/**
 * @psalm-type FileJson = array{
 *      type: "external"|"internal",
 *      internal?: array{ url: string, expiry_time: string },
 *      external?: array{ url: string },
 * }
 */
class File
{
    private const ALLOWED_TYPES = [ "external", "file" ];

    private string $type;
    private string $url;
    private DateTimeImmutable|null $expiryTime;

    private function __construct(
        string $type,
        string $url,
        DateTimeImmutable|null $expiryTime,
    ) {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new \Exception("Invalid file type: '{$type}'.");
        }

        $this->type = $type;
        $this->url = $url;
        $this->expiryTime = $expiryTime;
    }

    public static function createExternal(string $url): self
    {
        return new self("external", $url, null);
    }

    public static function createInternal(
        string $url,
        DateTimeImmutable|null $expiryTime = null
    ): self {
        return new self("internal", $url, $expiryTime);
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
            $type,
            $file["url"] ?? "",
            isset($file["expiry_time"]) ? new DateTimeImmutable($file["expiry_time"]) : null,
        );
    }

    public function toArray(): array
    {
        $array = [
            "type" => $this->type,
            "url"  => $this->url,
        ];

        if ($this->expiryTime !== null) {
            $array["expiry_time"] = $this->expiryTime;
        }

        return $array;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function expiryTime(): DateTimeImmutable|null
    {
        return $this->expiryTime;
    }

    public function isExternal(): bool
    {
        return $this->type === "external";
    }

    public function isInternal(): bool
    {
        return $this->type === "file";
    }

    public function withUrl(string $url): self
    {
        return new self($this->type, $url, $this->expiryTime);
    }
}
