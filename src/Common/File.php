<?php

namespace Notion\Common;

use DateTimeImmutable;

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

    public static function fromArray(array $array): self
    {
        return new self(
            $array["type"],
            $array["url"],
            isset($array["expiryTime"]) ? new DateTimeImmutable($array["expiryTime"]) : null,
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
}
