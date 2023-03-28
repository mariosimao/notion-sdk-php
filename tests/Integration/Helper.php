<?php

namespace Notion\Test\Integration;

use Exception;
use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;

final class Helper
{
    public static function client(): Notion
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            throw new Exception("Notion token is required to run integration tests.");
        }

        return Notion::create($token);
    }

    public static function testPageId(): string
    {
        $pageId = getenv("TEST_PAGE_ID");
        if (!$pageId) {
            throw new Exception("Test page ID required to run integration tests.");
        }

        return $pageId;
    }

    public static function newPage(): Page
    {
        return Page::create(PageParent::page(self::testPageId()));
    }
}
