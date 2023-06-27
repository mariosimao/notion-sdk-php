<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\Url;
use Notion\Pages\Properties\PropertyFactory;
use Notion\Pages\Properties\PropertyType;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function test_create(): void
    {
        $url = Url::create("https://notion.so");

        $this->assertTrue($url->metadata()->type === PropertyType::Url);
        $this->assertEquals("https://notion.so", $url->url);
    }

    public function test_create_empty(): void
    {
        $url = Url::createEmpty();

        $this->assertTrue($url->isEmpty());
    }

    public function test_change_url(): void
    {
        $url = Url::create("https://notion.so")->changeUrl("https://google.com");

        $this->assertEquals("https://google.com", $url->url);
    }

    public function test_clear(): void
    {
        $url = Url::create("https://notion.so")->clear();

        $this->assertTrue($url->isEmpty());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "url",
            "url" => "https://notion.so",
        ];

        $url = Url::fromArray($array);
        $fromFactory = PropertyFactory::fromArray($array);

        $this->assertEquals($array, $url->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }

    public function test_is_empty(): void
    {
        $array = [
            "id" => "abc",
            "type" => "url",
            "url" => null,
        ];

        $url = Url::fromArray($array);

        $this->assertTrue($url->isEmpty());
    }
}
