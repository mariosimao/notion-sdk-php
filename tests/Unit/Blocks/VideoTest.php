<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Blocks\Video;
use Notion\Common\Date;
use Notion\Common\File;
use PHPUnit\Framework\TestCase;

class VideoTest extends TestCase
{
    public function test_create_video(): void
    {
        $file = File::createExternal("https://my-site.com/video.mp4");
        $video = Video::create($file);

        $this->assertEquals($file, $video->file());
    }

    public function test_create_from_array(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "video",
            "video"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/video.mp4"
                ],
            ],
        ];

        $video = Video::fromArray($array);

        $this->assertEquals("https://my-site.com/video.mp4", $video->file()->url());

        $this->assertEquals($video, BlockFactory::fromArray($array));
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockTypeException::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "video"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/video.mp4"
                ],
            ],
        ];

        Video::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $file = File::createExternal("https://my-site.com/video.mp4");
        $video = Video::create($file);

        $expected = [
            "object"           => "block",
            "created_time"     => $video->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $video->block()->createdTime()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "video",
            "video"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/video.mp4"
                ],
            ],
        ];

        $this->assertEquals($expected, $video->toArray());
    }

    public function test_replace_file(): void
    {
        $file1 = File::createExternal("https://my-site.com/video1.mp4");
        $file2 = File::createExternal("https://my-site.com/video2.mp4");

        $old = Video::create($file1);
        $new = $old->withFile($file2);

        $this->assertEquals($file1, $old->file());
        $this->assertEquals($file2, $new->file());
    }
}
