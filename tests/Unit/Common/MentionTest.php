<?php

namespace Notion\Test\Unit\Common;

use DateTimeImmutable;
use Notion\Common\Date;
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
}
