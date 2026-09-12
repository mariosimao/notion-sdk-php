<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\TemplateMention;
use Notion\Common\TemplateMentionDateType;
use Notion\Common\TemplateMentionType;
use Notion\Common\TemplateMentionUserType;
use PHPUnit\Framework\TestCase;

class TemplateMentionTest extends TestCase
{
    public function test_template_mention_date(): void
    {
        $template = TemplateMention::date(TemplateMentionDateType::Today);

        $this->assertTrue($template->isDate());
        $this->assertFalse($template->isUser());
        $this->assertSame(TemplateMentionType::Date, $template->type);
        $this->assertSame(TemplateMentionDateType::Today, $template->templateMentionDate);
        $this->assertNull($template->templateMentionUser);
    }

    public function test_template_mention_today(): void
    {
        $template = TemplateMention::today();

        $this->assertTrue($template->isDate());
        $this->assertSame(TemplateMentionDateType::Today, $template->templateMentionDate);
    }

    public function test_template_mention_now(): void
    {
        $template = TemplateMention::now();

        $this->assertTrue($template->isDate());
        $this->assertSame(TemplateMentionDateType::Now, $template->templateMentionDate);
    }

    public function test_template_mention_user(): void
    {
        $template = TemplateMention::user(TemplateMentionUserType::Me);

        $this->assertTrue($template->isUser());
        $this->assertFalse($template->isDate());
        $this->assertSame(TemplateMentionType::User, $template->type);
        $this->assertSame(TemplateMentionUserType::Me, $template->templateMentionUser);
        $this->assertNull($template->templateMentionDate);
    }

    public function test_template_mention_me(): void
    {
        $template = TemplateMention::me();

        $this->assertTrue($template->isUser());
        $this->assertSame(TemplateMentionUserType::Me, $template->templateMentionUser);
    }

    public function test_template_mention_date_array_conversion(): void
    {
        $array = [
            "type" => "template_mention_date",
            "template_mention_date" => "today",
        ];

        $template = TemplateMention::fromArray($array);

        $this->assertSame($array, $template->toArray());
    }

    public function test_template_mention_now_array_conversion(): void
    {
        $array = [
            "type" => "template_mention_date",
            "template_mention_date" => "now",
        ];

        $template = TemplateMention::fromArray($array);

        $this->assertSame($array, $template->toArray());
    }

    public function test_template_mention_user_array_conversion(): void
    {
        $array = [
            "type" => "template_mention_user",
            "template_mention_user" => "me",
        ];

        $template = TemplateMention::fromArray($array);

        $this->assertSame($array, $template->toArray());
    }

    public function test_preserve_additional_subtype_data(): void
    {
        $array = [
            "type" => "template_mention_date",
            "template_mention_date" => "today",
            "custom_metadata" => "extra_value",
        ];

        /** @psalm-suppress InvalidArgument */
        $template = TemplateMention::fromArray($array);

        $this->assertSame($array, $template->toArray());
    }
}
