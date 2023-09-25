<?php

namespace Notion\Exceptions;

use Psr\Http\Message\ResponseInterface;

/**
 * Exception from Notion API
 */
class ApiException extends NotionException
{
    public readonly string $notionCode;
    public readonly ResponseInterface $response;

    final public function __construct(
        string $message,
        string $notionCode,
        ResponseInterface $response,
    ) {
        $this->notionCode = $notionCode;
        $this->response = $response;

        parent::__construct($message);
    }

    final public static function fromResponse(ResponseInterface $response): static
    {
        /** @var array{ message: string, code: string}|false|null $body */
        $body = json_decode((string) $response->getBody(), true);

        if ($body === null || $body === false) {
            return new static("", "", $response);
        }

        return match ($body["code"]) {
            "conflict_error" => new ConflictException($body["message"], $body["code"], $response),
            default          => new static($body["message"], $body["code"], $response),
        };
    }

    /**
     * @deprecated 1.3.0 This method will be removed in future versions. Use 'notionCode' property.
     */
    final public function getNotionCode(): string
    {
        return $this->notionCode;
    }
}
