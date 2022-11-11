<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Breadcrumb;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use Notion\Common\Date;
use PHPUnit\Framework\TestCase;

class BreadcrumbTest extends TestCase
{
    public function test_create_breadcrumb(): void
    {
        $breadcrumb = Breadcrumb::create();

        $this->assertEquals("breadcrumb", $breadcrumb->metadata()->type->value);
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
            "type"             => "breadcrumb",
            "breadcrumb"       => new \stdClass(),
        ];

        $breadcrumb = Breadcrumb::fromArray($array);

        $this->assertEquals($breadcrumb, BlockFactory::fromArray($array));
    }

    public function test_error_on_wrong_type(): void
    {
        $this->expectException(BlockException::class);
        $array = [
            "object"           => "block",
            "id"               => "04a13895-f072-4814-8af7-cd11af127040",
            "created_time"     => "2021-10-18T17:09:00.000Z",
            "last_edited_time" => "2021-10-18T17:09:00.000Z",
            "archived"         => false,
            "has_children"     => false,
            "type"             => "wrong-type",
            "breadcrumb"       => new \stdClass(),
        ];

        Breadcrumb::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $breadcrumb = Breadcrumb::create();

        $expected = [
            "object"           => "block",
            "created_time"     => $breadcrumb->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $breadcrumb->metadata()->createdTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "breadcrumb",
            "breadcrumb"       => new \stdClass(),
        ];

        $this->assertEquals($expected, $breadcrumb->toArray());
    }

    public function test_no_children_support(): void
    {
        $block = Breadcrumb::create();

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $block = Breadcrumb::create();

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_archive(): void
    {
        $block = Breadcrumb::create();

        $block = $block->archive();

        $this->assertTrue($block->metadata()->archived);
    }
}
