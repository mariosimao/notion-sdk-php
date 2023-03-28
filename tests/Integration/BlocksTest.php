<?php

namespace Notion\Test\Integration;

use Notion\Blocks\BlockType;
use Notion\Blocks\Bookmark;
use Notion\Blocks\Breadcrumb;
use Notion\Blocks\BulletedListItem;
use Notion\Blocks\Callout;
use Notion\Blocks\Code;
use Notion\Blocks\CodeLanguage;
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
use Notion\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

class BlocksTest extends TestCase
{
    public function test_create_page_change_all_blocks(): void
    {
        $client = Helper::client();
        $page = Helper::newPage()->changeTitle("Blocks test");

        $content = [
            Bookmark::fromUrl("https://notion.so"),
            Breadcrumb::create(),
            BulletedListItem::create()->changeText(RichText::fromString("List item ")),
            Callout::create()->changeText(RichText::fromString("Callout")),
            // TODO: Child database
            // TODO: Child page
            Code::fromText([
                RichText::fromString("<?php echo 'Hello world!';"),
            ], CodeLanguage::Php),
            Divider::create(),
            // TODO: Embed
            EquationBlock::fromString("a^2 + b^2 = c^2"),
            // TODO: File
            Heading1::fromText()->changeText(RichText::fromString("Heading 1")),
            Heading2::fromText()->changeText(RichText::fromString("Heading 2")),
            Heading3::fromText()->changeText(RichText::fromString("Heading 3")),
            // TODO: Image
            NumberedListItem::create()->changeText(RichText::fromString("List item ")),
            Paragraph::fromString("Paragraph"),
            // TODO: PDF
            TableOfContents::create(),
            ToDo::fromString("To do item"),
            Toggle::fromString("Toggle"),
            // TODO: Video
            ColumnList::create(
                Column::create(Paragraph::fromString("Paragraph")),
                Column::create(Paragraph::fromString("Paragraph")),
            ),
        ];

        $newPage = $client->pages()->create($page, $content);

        $newPageContent = $client->blocks()->findChildrenRecursive($newPage->id);

        foreach ($content as $index => $block) {
            $this->assertInstanceOf($block::class, $newPageContent[$index]);
        }

        $client->pages()->delete($newPage);
    }

    public function test_find_block(): void
    {
        $client = Helper::client();
        $page = Helper::newPage()->changeTitle("Blocks test");

        $content = [
            Heading1::fromText()->changeText(RichText::fromString("Heading 1")),
        ];

        $newPage = $client->pages()->create($page, $content);

        $children = $client->blocks()->findChildren($newPage->id);

        $block = $client->blocks()->find($children[0]->metadata()->id);

        $client->pages()->delete($newPage);

        $this->assertSame(BlockType::Heading1, $block->metadata()->type);
    }

    public function test_find_inexistent_block(): void
    {
        $client = Helper::client();

        $this->expectException(ApiException::class);
        $client->blocks()->find("inexistentId");
    }

    public function test_find_children_of_inexistent_block(): void
    {
        $client = Helper::client();

        $this->expectException(ApiException::class);
        $client->blocks()->findChildren("inexistentId");
    }

    public function test_delete_block(): void
    {
        $client = Helper::client();
        $page = Helper::newPage()->changeTitle("Blocks test");

        $content = [
            Heading1::fromText()->changeText(RichText::fromString("Heading 1")),
        ];

        $newPage = $client->pages()->create($page, $content);

        $childrenBeforeDelete = $client->blocks()->findChildren($newPage->id);

        $block = $childrenBeforeDelete[0];

        $deletedBlock = $client->blocks()->delete($block->metadata()->id);

        $childrenAfterDelete = $client->blocks()->findChildren($newPage->id);

        $this->assertTrue($deletedBlock->metadata()->archived);
        $this->assertEmpty($childrenAfterDelete);

        $client->pages()->delete($newPage);
    }

    public function test_delete_inexistent(): void
    {
        $client = Helper::client();

        $this->expectException(ApiException::class);
        $client->blocks()->delete("inexistentId");
    }

    public function test_add_block(): void
    {
        $client = Helper::client();

        $blocks = $client->blocks()->append(Helper::testPageId(), [
            Paragraph::fromString("This is a simple paragraph"),
        ]);

        foreach ($blocks as $block) {
            $client->blocks()->delete($block->metadata()->id);
        }

        $this->assertSame(BlockType::Paragraph, $blocks[0]->metadata()->type);
    }

    public function test_add_to_inexistent_block(): void
    {
        $client = Helper::client();

        $this->expectException(ApiException::class);
        $client->blocks()->append("inexistentId", [
            Paragraph::fromString("This is a simple paragraph"),
        ]);
    }

    public function test_update_block(): void
    {
        $client = Helper::client();

        $blocks = $client->blocks()->append(
            Helper::testPageId(),
            [
                Bookmark::fromUrl("https://notion.so"),
                Breadcrumb::create(),
                BulletedListItem::create()->changeText(RichText::fromString("List item ")),
                Callout::create()->changeText(RichText::fromString("Callout")),
                // TODO: Child database
                // TODO: Child page
                Code::fromText([
                    RichText::fromString("<?php echo 'Hello world!';"),
                ], CodeLanguage::Php),
                Divider::create(),
                // TODO: Embed
                EquationBlock::fromString("a^2 + b^2 = c^2"),
                // TODO: File
                Heading1::fromText()->changeText(RichText::fromString("Heading 1")),
                Heading2::fromText()->changeText(RichText::fromString("Heading 2")),
                Heading3::fromText()->changeText(RichText::fromString("Heading 3")),
                // TODO: Image
                NumberedListItem::create()->changeText(RichText::fromString("List item ")),
                Paragraph::fromString("Paragraph"),
                // TODO: PDF
                TableOfContents::create(),
                ToDo::fromString("To do item"),
                Toggle::fromString("Toggle"),
                // TODO: Video
                // TODO: ColumnList
            ]
        );

        foreach ($blocks as $block) {
            $block = $client->blocks()->update($block->archive());
            $archivedBlock = $client->blocks()->find($block->metadata()->id);
            $this->assertTrue($archivedBlock->metadata()->archived);
        }
    }

    public function test_update_newly_created_block(): void
    {
        $client = Helper::client();

        $paragraph = Paragraph::fromString("This is a simple paragraph");

        $this->expectException(ApiException::class);
        $client->blocks()->update($paragraph);
    }
}
