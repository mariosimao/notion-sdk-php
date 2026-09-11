<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\Audio;
use Notion\Blocks\BlockFactory;
use Notion\Blocks\Paragraph;
use Notion\Common\Date;
use Notion\Common\File;
use Notion\Common\RichText;
use Notion\Exceptions\BlockException;
use PHPUnit\Framework\TestCase;

class AudioTest extends TestCase
{
    public function test_create_audio(): void
    {
        $file = File::createExternal("https://my-site.com/audio.mp3");
        $audio = Audio::fromFile($file);

        $this->assertEquals($file, $audio->file);
    }

    public function test_create_from_url(): void
    {
        $audio = Audio::fromUrl("https://my-site.com/audio.mp3");

        $this->assertEquals("https://my-site.com/audio.mp3", $audio->file->url);
        $this->assertTrue($audio->file->isExternal());
    }

    public function test_create_from_array(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "audio",
            "audio"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/audio.mp3",
                ],
            ],
        ];

        $audio = Audio::fromArray($array);

        $this->assertEquals("https://my-site.com/audio.mp3", $audio->file->url);
        $this->assertEquals($audio, BlockFactory::fromArray($array));
    }

    public function test_create_from_array_with_caption(): void
    {
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "audio",
            "audio"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/audio.mp3",
                ],
                "caption" => [
                    [
                        "type"        => "text",
                        "text"        => [ "content" => "Podcast episode 1", "link" => null ],
                        "annotations" => [
                            "bold"          => false,
                            "italic"        => false,
                            "strikethrough" => false,
                            "underline"     => false,
                            "code"          => false,
                            "color"         => "default",
                        ],
                        "plain_text"  => "Podcast episode 1",
                        "href"        => null,
                    ],
                ],
            ],
        ];

        $audio = Audio::fromArray($array);

        $this->assertEquals("Podcast episode 1", $audio->file->caption[0]->plainText);
        $this->assertEquals($audio, BlockFactory::fromArray($array));
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockException::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "audio"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/audio.mp3",
                ],
            ],
        ];

        Audio::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $file = File::createExternal("https://my-site.com/audio.mp3");
        $audio = Audio::fromFile($file);

        $expected = [
            "object"           => "block",
            "created_time"     => $audio->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $audio->metadata()->createdTime->format(Date::FORMAT),
            "in_trash"         => false,
            "has_children"     => false,
            "type"             => "audio",
            "audio"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/audio.mp3",
                ],
            ],
        ];

        $this->assertEquals($expected, $audio->toArray());
    }

    public function test_replace_file(): void
    {
        $file1 = File::createExternal("https://my-site.com/audio1.mp3");
        $file2 = File::createExternal("https://my-site.com/audio2.mp3");

        $old = Audio::fromFile($file1);
        $new = $old->changeFile($file2);

        $this->assertEquals($file1, $old->file);
        $this->assertEquals($file2, $new->file);
    }

    public function test_change_url(): void
    {
        $audio = Audio::fromUrl("https://my-site.com/audio1.mp3");
        $new = $audio->changeUrl("https://my-site.com/audio2.mp3");

        $this->assertEquals("https://my-site.com/audio2.mp3", $new->file->url);
    }

    public function test_change_caption(): void
    {
        $audio = Audio::fromUrl("https://my-site.com/audio.mp3");
        $caption = RichText::fromString("Sample caption");
        $new = $audio->changeCaption($caption);

        $this->assertEquals([$caption], $new->file->caption);
    }

    public function test_no_children_support(): void
    {
        $audio = Audio::fromUrl("https://my-site.com/audio.mp3");

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $audio->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $audio = Audio::fromUrl("https://my-site.com/audio.mp3");

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $audio->addChild(Paragraph::create());
    }

    public function test_delete(): void
    {
        $audio = Audio::fromUrl("https://my-site.com/audio.mp3");
        $deleted = $audio->delete();

        $this->assertTrue($deleted->metadata()->inTrash);
    }

    public function test_archive(): void
    {
        $audio = Audio::fromUrl("https://my-site.com/audio.mp3");
        /** @psalm-suppress DeprecatedMethod */
        $archived = $audio->archive();

        $this->assertTrue($archived->metadata()->inTrash);
    }
}
