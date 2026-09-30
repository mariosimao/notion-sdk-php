<?php

namespace Notion\Test\Unit\Pages;

use Notion\DataSources\Template;
use Notion\Pages\PageTemplate;
use Notion\Pages\PageTemplateType;
use PHPUnit\Framework\TestCase;

class PageTemplateTest extends TestCase
{
    public function test_none(): void
    {
        $template = PageTemplate::none();

        $this->assertTrue($template->isNone());
        $this->assertSame([ "type" => "none" ], $template->toArray());
    }

    public function test_default(): void
    {
        $template = PageTemplate::default();

        $this->assertTrue($template->isDefault());
        $this->assertSame([ "type" => "default" ], $template->toArray());
    }

    public function test_default_with_timezone(): void
    {
        $template = PageTemplate::default("America/New_York");

        $this->assertSame("America/New_York", $template->timezone);
        $this->assertSame([ "type" => "default", "timezone" => "America/New_York" ], $template->toArray());
    }

    public function test_from_id(): void
    {
        $template = PageTemplate::fromId("a7e80c0b-a766-43c3-a9e9-21ce94595e0e", "Europe/London");

        $this->assertTrue($template->isTemplateId());
        $this->assertSame(PageTemplateType::TemplateId, $template->type);
        $this->assertSame(
            [
                "type" => "template_id",
                "template_id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
                "timezone" => "Europe/London",
            ],
            $template->toArray(),
        );
    }

    public function test_from_template(): void
    {
        $dataSourceTemplate = Template::fromArray([
            "id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e",
            "name" => "Bug",
            "is_default" => false,
        ]);

        $template = PageTemplate::fromTemplate($dataSourceTemplate);

        $this->assertSame(
            [ "type" => "template_id", "template_id" => "a7e80c0b-a766-43c3-a9e9-21ce94595e0e" ],
            $template->toArray(),
        );
    }
}
