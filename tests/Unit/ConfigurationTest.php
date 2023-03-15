<?php

namespace Notion\Test\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Notion\Configuration;
use Notion\Notion;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    public function test_create_default_configuration(): void
    {
        $token = "secret_123abc";
        $config = Configuration::create($token);

        $this->assertSame($token, $config->token);
        $this->assertSame(Notion::API_VERSION, $config->version);
        $this->assertSame(true, $config->retryOnConflict);
        $this->assertSame(3, $config->retryOnConflictAttempts);
    }

    public function test_create_from_psr_implementations(): void
    {
        $token = "secret_123abc";
        $client = new Client();
        $factory = new HttpFactory();

        $config = Configuration::createFromPsrImplementations($token, $client, $factory);

        $this->assertSame($client, $config->httpClient);
        $this->assertSame($factory, $config->requestFactory);
    }

    public function test_enable_retry_on_conflict(): void
    {
        $config = Configuration::create("secret_123abc")->enableRetryOnConflict(3);

        $this->assertTrue($config->retryOnConflict);
        $this->assertSame(3, $config->retryOnConflictAttempts);
    }

    public function test_disable_retry_on_conflict(): void
    {
        $config = Configuration::create("secret_123abc")->disableRetryOnConflict();

        $this->assertFalse($config->retryOnConflict);
        $this->assertSame(0, $config->retryOnConflictAttempts);
    }
}
