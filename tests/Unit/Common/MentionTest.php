<?php

namespace Notion\Test\Unit\Common;

use DateTimeImmutable;
use Notion\Common\CustomEmoji;
use Notion\Common\Date;
use Notion\Common\LinkMention;
use Notion\Common\Mention;
use Notion\Common\MentionType;
use Notion\Common\TemplateMention;
use Notion\Common\TemplateMentionDateType;
use Notion\Common\TemplateMentionUserType;
use Notion\Users\User;
use PHPUnit\Framework\TestCase;

class MentionTest extends TestCase
{
    public function test_mention_page(): void
    {
        $mention = Mention::page("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");

        $this->assertTrue($mention->isPage());
        $this->assertEquals(MentionType::Page, $mention->type);
        $this->assertEquals("1ce62b6f-b7f3-4201-afd0-08acb02e61c6", $mention->pageId);
    }

    public function test_mention_database(): void
    {
        $mention = Mention::database("1ce62b6f-b7f3-4201-afd0-08acb02e61c6");

        $this->assertTrue($mention->isDatabase());
        $this->assertEquals("1ce62b6f-b7f3-4201-afd0-08acb02e61c6", $mention->databaseId);
    }

    public function test_mention_user(): void
    {
        $user = User::fromArray([
            "object"     => "user",
            "id"         => "b0688871-85db-4637-8fc9-043a240fcaec",
            "name"       => "Mario Simao",
            "avatar_url" => "http://example.com",
            "type"       => "person",
            "person"     => [ "email" => "mariosimao@email.com" ],
        ]);

        $mention = Mention::user($user);

        $this->assertTrue($mention->isUser());
        $this->assertEquals($user, $mention->user);
    }

    public function test_mention_date(): void
    {
        $date = Date::create(new DateTimeImmutable("2021-01-01"));
        $mention = Mention::date($date);

        $this->assertTrue($mention->isDate());
        $this->assertEquals($date, $mention->date);
    }

    public function test_page_array_conversion(): void
    {
        $array = [
            "type" => "page",
            "page" => [ "id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6" ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_database_array_conversion(): void
    {
        $array = [
            "type" => "database",
            "database" => [ "id" => "1ce62b6f-b7f3-4201-afd0-08acb02e61c6" ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_user_array_conversion(): void
    {
        $array = [
            "type" => "user",
            "user" => [
                "object"     => "user",
                "id"         => "b0688871-85db-4637-8fc9-043a240fcaec",
                "name"       => "Mario Simao",
                "avatar_url" => "http://example.com",
                "type"       => "person",
                "person"     => [ "email" => "mariosimao@email.com" ],
            ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_date_array_conversion(): void
    {
        $array = [
            "type" => "date",
            "date" => [ "start" => "2021-01-01T00:00:00.000000Z", "end" => null ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_mention_link_preview(): void
    {
        $mention = Mention::linkPreview("https://notion.so");

        $this->assertTrue($mention->isLinkPreview());
        $this->assertEquals(MentionType::LinkPreview, $mention->type);
        $this->assertEquals("https://notion.so", $mention->linkPreviewUrl);
    }

    public function test_mention_template_mention(): void
    {
        $template = TemplateMention::today();
        $mention = Mention::templateMention($template);

        $this->assertTrue($mention->isTemplateMention());
        $this->assertEquals(MentionType::TemplateMention, $mention->type);
        $this->assertEquals($template, $mention->templateMention);
    }

    public function test_mention_template_date(): void
    {
        $mention = Mention::templateDate(TemplateMentionDateType::Today);

        $this->assertTrue($mention->isTemplateMention());
        $this->assertEquals(TemplateMentionDateType::Today, $mention->templateMention?->templateMentionDate);
    }

    public function test_mention_template_user(): void
    {
        $mention = Mention::templateUser(TemplateMentionUserType::Me);

        $this->assertTrue($mention->isTemplateMention());
        $this->assertEquals(TemplateMentionUserType::Me, $mention->templateMention?->templateMentionUser);
    }

    public function test_mention_today(): void
    {
        $mention = Mention::today();

        $this->assertTrue($mention->isTemplateMention());
        $this->assertEquals(TemplateMentionDateType::Today, $mention->templateMention?->templateMentionDate);
    }

    public function test_mention_now(): void
    {
        $mention = Mention::now();

        $this->assertTrue($mention->isTemplateMention());
        $this->assertEquals(TemplateMentionDateType::Now, $mention->templateMention?->templateMentionDate);
    }

    public function test_mention_me(): void
    {
        $mention = Mention::me();

        $this->assertTrue($mention->isTemplateMention());
        $this->assertEquals(TemplateMentionUserType::Me, $mention->templateMention?->templateMentionUser);
    }

    public function test_link_preview_array_conversion(): void
    {
        $array = [
            "type" => "link_preview",
            "link_preview" => [ "url" => "https://notion.so" ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_template_mention_date_array_conversion(): void
    {
        $array = [
            "type" => "template_mention",
            "template_mention" => [
                "type" => "template_mention_date",
                "template_mention_date" => "today",
            ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_template_mention_user_array_conversion(): void
    {
        $array = [
            "type" => "template_mention",
            "template_mention" => [
                "type" => "template_mention_user",
                "template_mention_user" => "me",
            ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_mention_link_mention(): void
    {
        $mention = Mention::linkMention("https://github.com/mariosimao/notion-sdk-php");

        $this->assertTrue($mention->isLinkMention());
        $this->assertEquals(MentionType::LinkMention, $mention->type);
        $this->assertEquals("https://github.com/mariosimao/notion-sdk-php", $mention->linkMention?->href);
        $this->assertNull($mention->linkMention?->title);
    }

    public function test_mention_link_mention_object(): void
    {
        $linkMention = LinkMention::create("https://notion.so");
        $mention = Mention::linkMention($linkMention);

        $this->assertTrue($mention->isLinkMention());
        $this->assertSame($linkMention, $mention->linkMention);
    }

    public function test_link_mention_array_conversion(): void
    {
        $array = [
            "type" => "link_mention",
            "link_mention" => [
                "href"          => "https://github.com/mariosimao/notion-sdk-php",
                "title"         => "notion-sdk-php",
                "description"   => "A complete Notion SDK for PHP developers.",
                "link_author"   => "mariosimao",
                "link_provider" => "GitHub",
                "thumbnail_url" => "https://example.com/thumbnail.png",
                "icon_url"      => "https://github.com/favicon.ico",
                "iframe_url"    => "https://example.com/iframe",
                "height"        => 200,
                "padding"       => 10,
                "padding_top"   => 5,
            ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertTrue($mention->isLinkMention());
        $linkMention = $mention->linkMention;
        $this->assertEquals("notion-sdk-php", $linkMention?->title);
        $this->assertEquals("A complete Notion SDK for PHP developers.", $linkMention?->description);
        $this->assertEquals("mariosimao", $linkMention?->linkAuthor);
        $this->assertEquals("GitHub", $linkMention?->linkProvider);
        $this->assertEquals("https://example.com/thumbnail.png", $linkMention?->thumbnailUrl);
        $this->assertEquals("https://github.com/favicon.ico", $linkMention?->iconUrl);
        $this->assertEquals("https://example.com/iframe", $linkMention?->iframeUrl);
        $this->assertEquals(200, $linkMention?->height);
        $this->assertEquals(10, $linkMention?->padding);
        $this->assertEquals(5, $linkMention?->paddingTop);
        $this->assertEquals($array, $mention->toArray());
    }

    public function test_link_mention_minimal_array_conversion(): void
    {
        $array = [
            "type" => "link_mention",
            "link_mention" => [ "href" => "https://notion.so" ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertEquals($array, $mention->toArray());
    }

    public function test_mention_custom_emoji(): void
    {
        $emoji = CustomEmoji::create(
            "45ce454c-d427-4f53-9489-e5d0f3d1db6b",
            "bufo",
            "https://example.com/bufo.png",
        );
        $mention = Mention::customEmoji($emoji);

        $this->assertTrue($mention->isCustomEmoji());
        $this->assertEquals(MentionType::CustomEmoji, $mention->type);
        $this->assertEquals("45ce454c-d427-4f53-9489-e5d0f3d1db6b", $mention->customEmoji?->id);
        $this->assertEquals("bufo", $mention->customEmoji?->name);
        $this->assertEquals("https://example.com/bufo.png", $mention->customEmoji?->url);
    }

    public function test_custom_emoji_array_conversion(): void
    {
        $array = [
            "type" => "custom_emoji",
            "custom_emoji" => [
                "id"   => "45ce454c-d427-4f53-9489-e5d0f3d1db6b",
                "name" => "bufo",
                "url"  => "https://example.com/bufo.png",
            ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertTrue($mention->isCustomEmoji());
        $this->assertEquals($array, $mention->toArray());
    }

    public function test_custom_emoji_only_id_array_conversion(): void
    {
        $array = [
            "type" => "custom_emoji",
            "custom_emoji" => [ "id" => "45ce454c-d427-4f53-9489-e5d0f3d1db6b" ],
        ];
        $mention = Mention::fromArray($array);

        $this->assertNull($mention->customEmoji?->name);
        $this->assertNull($mention->customEmoji?->url);
        $this->assertEquals($array, $mention->toArray());
    }
}
