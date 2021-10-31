<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Pages\Properties\Url;
use Notion\Pages\Properties\Factory;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function test_create(): void
    {
        $url = Url::create("https://notion.so");

        $this->assertTrue($url->property()->isUrl());
        $this->assertEquals("https://notion.so", $url->url());
    }

    public function test_change_url(): void
    {
        $url = Url::create("https://notion.so")->withUrl("https://google.com");

        $this->assertEquals("https://google.com", $url->url());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "url",
            "url" => "https://notion.so",
        ];

        $url = Url::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $url->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
