<?php

namespace Notion\Test\Unit\Pages\Properties;

use Notion\Common\File;
use Notion\Pages\Properties\Factory;
use Notion\Pages\Properties\Files;
use PHPUnit\Framework\TestCase;

class FilesTest extends TestCase
{
    public function test_create(): void
    {
        $myFile = File::createExternal("https://example.com/image.png");
        $files = Files::create([$myFile]);

        $this->assertTrue($files->property()->isFiles());
        $this->assertEquals("https://example.com/image.png", $files->files()[0]->url());
    }

    public function test_add_file(): void
    {
        $myFile1 = File::createExternal("https://example.com/image1.png");
        $myFile2 = File::createExternal("https://example.com/image2.png");

        $files = Files::create([$myFile1])->withAddedFile($myFile2);

        $this->assertCount(2, $files->files());
    }

    public function test_change_files(): void
    {
        $myFile1 = File::createExternal("https://example.com/image1.png");
        $myFile2 = File::createExternal("https://example.com/image2.png");

        $files = Files::create([$myFile1])->withFiles([$myFile2]);

        $this->assertCount(1, $files->files());
        $this->assertEquals("https://example.com/image2.png", $files->files()[0]->url());
    }

    public function test_array_conversion(): void
    {
        $array = [
            "id" => "abc",
            "type" => "files",
            "files" => [
                [
                    "type" => "external",
                    "external" => [
                        "url"  => "https://example.com/image.png",
                    ],
                ],
            ],
        ];

        $files = Files::fromArray($array);
        $fromFactory = Factory::fromArray($array);

        $this->assertEquals($array, $files->toArray());
        $this->assertEquals($array, $fromFactory->toArray());
    }
}
