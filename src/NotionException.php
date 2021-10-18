<?php

namespace Notion;

use Throwable;

class NotionException extends \Exception
{
    private string $errorCode;

    public function __construct($message, $errorCode)
    {
        $this->errorCode = $errorCode;

        parent::__construct($message);
    }

    public function errorCode(): string {
        return $this->errorCode;
    }
}
