<?php

namespace Notion\Test\Unit\Common;

use DateTimeImmutable;
use Notion\Common\File;
use Notion\Common\FileType;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function test_create_internal(): void
    {
        $expiryTime = new DateTimeImmutable("2021-01-01");
        $file = File::createInternal("https://notion.so/image.png", $expiryTime);

        $this->assertTrue($file->isInternal());
        $this->assertEquals(FileType::Internal, $file->type);
        $this->assertEquals("https://notion.so/image.png", $file->url);
        $this->assertEquals($expiryTime, $file->expiryTime);
    }

    public function test_create_external(): void
    {
        $file = File::createExternal("https://my-site.com/image.png");

        $this->assertTrue($file->isExternal());
        $this->assertEquals(FileType::External, $file->type);
        $this->assertEquals("https://my-site.com/image.png", $file->url);
        $this->assertNull($file->expiryTime);
    }

    public function test_intenral_array_conversion(): void
    {
        $array = [
            "type" => "file",
            "file" => [
                "url" => "https://notion.so/image.png",
                "expiry_time" => "2020-12-08T12:00:00.000000Z",
            ],
        ];
        $file = File::fromArray($array);

        $this->assertEquals($array, $file->toArray());
    }

    public function test_external_array_conversion(): void
    {
        $array = [
            "type" => "external",
            "external" => [ "url" => "https://my-site.com/image.png" ],
        ];
        $file = File::fromArray($array);

        $this->assertEquals($array, $file->toArray());
    }

    public function test_change_url(): void
    {
        $file = File::createExternal("")->changeUrl("https://my-site.com/image.png");

        $this->assertEquals("https://my-site.com/image.png", $file->url);
    }
}
