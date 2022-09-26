<?php

namespace Notion\Exceptions;

/**
 * Exception from Notion API
 */
final class ApiException extends NotionException
{
    private string $notionCode;

    private function __construct(string $message, string $notionCode)
    {
        $this->notionCode = $notionCode;

        parent::__construct($message);
    }

    /** @param array{ message: string, code: string} $body */
    public static function fromResponseBody(array $body): self
    {
        return new self($body["message"], $body["code"]);
    }

    public function getNotionCode(): string
    {
        return $this->notionCode;
    }
}
