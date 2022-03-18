<?php

namespace Notion\Test\Integration;

use Notion\Blocks\Bookmark;
use Notion\Blocks\Breadcrumb;
use Notion\Blocks\BulletedListItem;
use Notion\Blocks\Callout;
use Notion\Blocks\Code;
use Notion\Blocks\Column;
use Notion\Blocks\ColumnList;
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
            ColumnList::create([
                Column::create([ Paragraph::fromString("Paragraph") ]),
                Column::create([ Paragraph::fromString("Paragraph") ]),
            ]),
        ];

        $newPage = $client->pages()->create($page, $content);

        $newPageContent = $client->blocks()->findChildrenRecursive($newPage->id());

        foreach ($content as $index => $block) {
            $this->assertInstanceOf($block::class, $newPageContent[$index]);
        }

        $client->pages()->delete($newPage);
    }

    public function test_find_block(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $page = Page::create(PageParent::page(self::DEFAULT_PARENT_ID))->withTitle("Blocks test");

        $content = [
            Heading1::create()->withText([ RichText::createText("Heading 1") ]),
        ];

        $newPage = $client->pages()->create($page, $content);

        $children = $client->blocks()->findChildren($newPage->id());

        $block = $client->blocks()->find($children[0]->block()->id());

        $client->pages()->delete($newPage);

        $this->assertTrue($block->block()->isHeading1());
    }

    public function test_find_inexistent_block(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $this->expectException(NotionException::class);
        $client->blocks()->find("inexistentId");
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

    public function test_delete_block(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $page = Page::create(PageParent::page(self::DEFAULT_PARENT_ID))->withTitle("Blocks test");

        $content = [
            Heading1::create()->withText([ RichText::createText("Heading 1") ]),
        ];

        $newPage = $client->pages()->create($page, $content);

        $childrenBeforeDelete = $client->blocks()->findChildren($newPage->id());

        $block = $childrenBeforeDelete[0];

        $deletedBlock = $client->blocks()->delete($block->block()->id());

        $childrenAfterDelete = $client->blocks()->findChildren($newPage->id());

        $this->assertTrue($deletedBlock->block()->archived());
        $this->assertEmpty($childrenAfterDelete);

        $client->pages()->delete($newPage);
    }

    public function test_delete_inexistent(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $this->expectException(NotionException::class);
        $client->blocks()->delete("inexistentId");
    }

    public function test_append_block(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $blocks = $client->blocks()->append(
            self::DEFAULT_PARENT_ID,
            [
                Paragraph::fromString("This is a simple paragraph"),
            ]
        );

        foreach ($blocks as $block) {
            $client->blocks()->delete($block->block()->id());
        }

        $this->assertTrue($blocks[0]->block()->isParagraph());
    }

    public function test_append_to_inexistent_block(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $this->expectException(NotionException::class);
        $client->blocks()->append(
            "inexistentId",
            [
                Paragraph::fromString("This is a simple paragraph"),
            ]
        );
    }

    public function test_update_block(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $blocks = $client->blocks()->append(
            self::DEFAULT_PARENT_ID,
            [
                Paragraph::fromString("This is a simple paragraph"),
            ]
        );

        /** @var Paragraph $paragraph */
        $paragraph = $blocks[0];
        $paragraph = $paragraph->withText([
            RichText::createText("This is a simple paragraph updated.")
        ]);

        /** @var Paragraph $updatedParagraph */
        $updatedParagraph = $client->blocks()->update($paragraph);

        $this->assertSame("This is a simple paragraph updated.", $updatedParagraph->toString());

        // Teardown
        foreach ($blocks as $block) {
            $client->blocks()->delete($block->block()->id());
        }
    }

    public function test_update_newly_created_block(): void
    {
        $token = getenv("NOTION_TOKEN");
        if (!$token) {
            $this->markTestSkipped("Notion token is required to run integration tests.");
        }
        $client = Notion::create($token);

        $paragraph = Paragraph::fromString("This is a simple paragraph");

        $this->expectException(NotionException::class);
        $client->blocks()->update($paragraph);
    }
}
