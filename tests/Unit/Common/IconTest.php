<?php

namespace Notion\Test\Unit\Common;

use Notion\Common\File;
use Notion\Common\Icon;
use PHPUnit\Framework\TestCase;

class IconTest extends TestCase
{
    public function test_icon_from_file_array_conversion(): void
    {
        $file = File::createExternal("http://example.com/icon.png");
        $icon = Icon::fromFile($file);

        $expected = [
            "type" => "external",
            "external" => ["url" => "http://example.com/icon.png"],
        ];

        $this->assertEquals($expected, $icon->toArray());
    }
}
