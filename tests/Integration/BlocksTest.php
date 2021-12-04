<?php

namespace Notion\Test\Integration;

use Notion\Blocks\Bookmark;
use Notion\Blocks\Breadcrumb;
use Notion\Blocks\BulletedListItem;
use Notion\Blocks\Callout;
use Notion\Blocks\Code;
use Notion\Blocks\Divider;
use Notion\Blocks\EquationBlock;
use Notion\Blocks\Heading1;
use Notion\Blocks\Heading2;
use Notion\Blocks\Heading3;
use Notion\Blocks\NumberedListItem;
use Notion\Blocks\Paragraph;
use Notion\Blocks\TableOfContents;
use Notion\Blocks\ToDo;
use Notion\Blocks\Toggle;
use Notion\Common\RichText;
use Notion\Notion;
use Notion\NotionException;
use Notion\Pages\Page;
use Notion\Pages\PageParent;
use PHPUnit\Framework\TestCase;

class BlocksTest extends TestCase
{
    private const DEFAULT_PARENT_ID = "3f4c46dee17f43b79587094b61407a31";

    public function test_create_page_with_all_blocks(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $page = Page::create(PageParent::page(self::DEFAULT_PARENT_ID))->withTitle("Blocks test");

        $content = [
            Bookmark::create("https://notion.so"),
            Breadcrumb::create(),
            BulletedListItem::create()->withText([ RichText::createText("List item ")]),
            Callout::create()->withText([ RichText::createText("Callout") ]),
            // TODO: Child database
            // TODO: Child page
            Code::create("<?php echo 'Hello world!';", Code::LANG_PHP),
            Divider::create(),
            // TODO: Embed
            EquationBlock::create("a^2 + b^2 = c^2"),
            // TODO: File
            Heading1::create()->withText([ RichText::createText("Heading 1") ]),
            Heading2::create()->withText([ RichText::createText("Heading 2") ]),
            Heading3::create()->withText([ RichText::createText("Heading 3") ]),
            // TODO: Image
            NumberedListItem::create()->withText([ RichText::createText("List item ")]),
            Paragraph::fromString("Paragraph"),
            // TODO: PDF
            TableOfContents::create(),
            ToDo::fromString("To do item"),
            Toggle::fromString("Toggle"),
            // TODO: Video
        ];

        $newPage = $client->pages()->create($page, $content);

        $newPageContent = $client->blocks()->findChildrenRecursive($newPage->id());

        foreach ($content as $index => $block) {
            $this->assertInstanceOf($block::class, $newPageContent[$index]);
        }

        $client->pages()->delete($newPage);
    }

    public function test_find_children_of_inexistent_block(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $this->expectException(NotionException::class);
        $client->blocks()->findChildren("inexistentId");
    }
}
