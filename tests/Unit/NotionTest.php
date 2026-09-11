<?php

namespace Notion\Test\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Notion\Notion;
use PHPUnit\Framework\TestCase;

class NotionTest extends TestCase
{
    public function test_custom_psr_implementation(): void
    {
        $psrClient = new Client();
        $requestFactory = new HttpFactory();
        $token = "secret_token";

        $notion = Notion::createWithPsrImplementations(
            $psrClient,
            $requestFactory,
            $token,
        );

        $this->assertInstanceOf(Notion::class, $notion);
    }

    public function test_authentication(): void
    {
        $notion = Notion::create("secret_token");

        $this->assertInstanceOf(
            \Notion\Authentication\Client::class,
            $notion->authentication(),
        );
    }
}
