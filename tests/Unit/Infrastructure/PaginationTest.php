<?php

namespace Notion\Test\Unit\Infrastructure;

use Notion\Infrastructure\Pagination;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    public function test_url_without_parameters(): void
    {
        $this->assertSame(
            "https://api.notion.com/v1/users",
            Pagination::url("https://api.notion.com/v1/users", null, null),
        );
    }

    public function test_url_with_cursor_and_page_size(): void
    {
        $this->assertSame(
            "https://api.notion.com/v1/users?start_cursor=abc-123&page_size=50",
            Pagination::url("https://api.notion.com/v1/users", "abc-123", 50),
        );
    }

    public function test_url_with_extra_parameters(): void
    {
        $this->assertSame(
            "https://api.notion.com/v1/comments?block_id=b1&page_size=10",
            Pagination::url("https://api.notion.com/v1/comments", null, 10, [ "block_id" => "b1" ]),
        );
    }

    public function test_url_with_only_extra_parameters(): void
    {
        $this->assertSame(
            "https://api.notion.com/v1/file_uploads?status=pending",
            Pagination::url("https://api.notion.com/v1/file_uploads", null, null, [ "status" => "pending" ]),
        );
    }

    public function test_max_page_size(): void
    {
        $this->assertSame(100, Pagination::MAX_PAGE_SIZE);
    }
}
