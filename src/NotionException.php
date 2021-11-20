<?php

namespace Notion;

use Throwable;

class NotionException extends \Exception
{
    private string $errorCode;

    public function __construct(string $message, string $errorCode)
    {
        $this->errorCode = $errorCode;

        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
