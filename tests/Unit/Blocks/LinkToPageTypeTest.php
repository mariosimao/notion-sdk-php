<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\LinkToPageType;
use PHPUnit\Framework\TestCase;

class LinkToPageTypeTest extends TestCase
{
    public function test_cases(): void
    {
        $this->assertSame("page_id", LinkToPageType::Page->value);
        $this->assertSame("database_id", LinkToPageType::Database->value);
        $this->assertSame("comment_id", LinkToPageType::Comment->value);
    }
}
