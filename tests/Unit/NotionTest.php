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
        $authClient = $notion->authentication("client_id", "client_secret");

        $this->assertInstanceOf(
            \Notion\Authentication\Client::class,
            $authClient,
        );
        $this->assertSame("client_id", $authClient->clientId);
        $this->assertSame("client_secret", $authClient->clientSecret);
    }
}
