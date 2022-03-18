<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Breadcrumb;
use Notion\Blocks\Exceptions\BlockTypeException;
use Notion\Common\Date;
use Notion\NotionException;
use PHPUnit\Framework\TestCase;

class BreadcrumbTest extends TestCase
{
    public function test_create_breadcrumb(): void
    {
        $breadcrumb = Breadcrumb::create();

        $this->assertEquals("breadcrumb", $breadcrumb->block()->type());
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
            "breadcrumb"       => [],
        ];

        $breadcrumb = Breadcrumb::fromArray($array);

        $this->assertTrue($breadcrumb->block()->isBreadcrumb());

        $this->assertEquals($breadcrumb, BlockFactory::fromArray($array));
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
            "breadcrumb"       => [],
        ];

        Breadcrumb::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $breadcrumb = Breadcrumb::create();

        $expected = [
            "object"           => "block",
            "created_time"     => $breadcrumb->block()->createdTime()->format(Date::FORMAT),
            "last_edited_time" => $breadcrumb->block()->createdTime()->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "breadcrumb",
            "breadcrumb"       => [],
        ];

        $this->assertEquals($expected, $breadcrumb->toArray());
    }

    public function test_no_children_support(): void
    {
        $block = Breadcrumb::create();

        $this->expectException(NotionException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren([]);
    }

    public function test_array_for_update_operations(): void
    {
        $block = Breadcrumb::create();

        $array = $block->toUpdateArray();

        $this->assertCount(2, $array);
    }
}
