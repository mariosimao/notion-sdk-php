<?php

namespace Notion\Test\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Notion\Configuration;
use Notion\Notion;
use Notion\RetryPolicy;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    public function test_create_default_configuration(): void
    {
        $token = "secret_123abc";
        $config = Configuration::create($token);

        $this->assertSame($token, $config->token);
        $this->assertSame(Notion::API_VERSION, $config->version);
        $this->assertEquals(RetryPolicy::create(), $config->retryPolicy);
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

    public function test_with_retry_policy(): void
    {
        $policy = RetryPolicy::create(maxRetries: 5);
        $config = Configuration::create("secret_123abc")->withRetryPolicy($policy);

        $this->assertSame($policy, $config->retryPolicy);
    }

    public function test_without_retries(): void
    {
        $config = Configuration::create("secret_123abc")->withoutRetries();

        $this->assertSame(0, $config->retryPolicy->maxRetries);
    }
}
