<?php

namespace Notion\Test\Unit\Blocks;

use Notion\Blocks\BlockFactory;
use Notion\Blocks\Paragraph;
use Notion\Exceptions\BlockException;
use Notion\Blocks\Pdf;
use Notion\Common\Date;
use Notion\Common\File;
use PHPUnit\Framework\TestCase;

class PdfTest extends TestCase
{
    public function test_create_pdf(): void
    {
        $file = File::createExternal("https://my-site.com/document.pdf");
        $pdf = Pdf::fromFile($file);

        $this->assertEquals($file, $pdf->file);
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
            "type"             => "pdf",
            "pdf"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/document.pdf"
                ],
            ],
        ];

        $pdf = Pdf::fromArray($array);

        $this->assertEquals("https://my-site.com/document.pdf", $pdf->file->url);

        $this->assertEquals($pdf, BlockFactory::fromArray($array));
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
            "pdf"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/document.pdf"
                ],
            ],
        ];

        Pdf::fromArray($array);
    }

    public function test_transform_in_array(): void
    {
        $file = File::createExternal("https://my-site.com/document.pdf");
        $pdf = Pdf::fromFile($file);

        $expected = [
            "object"           => "block",
            "created_time"     => $pdf->metadata()->createdTime->format(Date::FORMAT),
            "last_edited_time" => $pdf->metadata()->createdTime->format(Date::FORMAT),
            "archived"         => false,
            "has_children"     => false,
            "type"             => "pdf",
            "pdf"            => [
                "type"     => "external",
                "external" => [
                    "url" => "https://my-site.com/document.pdf"
                ],
            ],
        ];

        $this->assertEquals($expected, $pdf->toArray());
    }

    public function test_replace_file(): void
    {
        $file1 = File::createExternal("https://my-site.com/pdf1.png");
        $file2 = File::createExternal("https://my-site.com/pdf2.png");

        $old = Pdf::fromFile($file1);
        $new = $old->changeFile($file2);

        $this->assertEquals($file1, $old->file);
        $this->assertEquals($file2, $new->file);
    }

    public function test_no_children_support(): void
    {
        $file = File::createExternal("https://my-site.com/document.pdf");
        $block = Pdf::fromFile($file);

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->changeChildren();
    }

    public function test_no_children_support_2(): void
    {
        $file = File::createExternal("https://my-site.com/document.pdf");
        $block = Pdf::fromFile($file);

        $this->expectException(BlockException::class);
        /** @psalm-suppress UnusedMethodCall */
        $block->addChild(Paragraph::create());
    }

    public function test_archive(): void
    {
        $file = File::createExternal("https://example.com/document.pdf");
        $block = Pdf::fromFile($file);

        $block = $block->archive();

        $this->assertTrue($block->metadata()->archived);
    }
}
