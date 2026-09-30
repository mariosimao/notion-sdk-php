# Work with markdown content

Pages can be created, read and updated using Notion's
[enhanced markdown](https://developers.notion.com/guides/data-apis/enhanced-markdown).

## Create a page from markdown

```php
<?php

use Notion\Notion;
use Notion\Pages\Page;
use Notion\Pages\PageParent;

$notion = Notion::create($_ENV["NOTION_SECRET"]);

$page = Page::create(PageParent::page("c986d7b0-7051-4f18-b165-cc0b9503ffc2"))
    ->changeTitle("Release notes");

$page = $notion->pages()->createFromMarkdown($page, "## Highlights\n\n- Markdown support");
```

## Retrieve a page as markdown

```php
$markdown = $notion->pages()->findMarkdown($pageId);
// Include full meeting note transcripts
$markdown = $notion->pages()->findMarkdown($pageId, includeTranscript: true);

echo $markdown->markdown;

// Large pages may be truncated; fetch missing subtrees by ID
foreach ($markdown->unknownBlockIds as $blockId) {
    $subtree = $notion->pages()->findMarkdown($blockId);
}
```

## Update a page's markdown

Every update returns the full page content as markdown.

```php
use Notion\Pages\Markdown\ContentUpdate;
use Notion\Pages\Markdown\InsertContent;
use Notion\Pages\Markdown\ReplaceContent;
use Notion\Pages\Markdown\ReplaceContentRange;
use Notion\Pages\Markdown\UpdateContent;

$pages = $notion->pages();

// Search-and-replace (recommended)
$pages->updateMarkdown($pageId, UpdateContent::create(
    ContentUpdate::create("Draft", "Final")->replaceAllMatches(),
    ContentUpdate::create("TODO", "Done"),
));

// Replace the whole page content (recommended)
$pages->updateMarkdown($pageId, ReplaceContent::create("# New content"));

// Insert content
$pages->updateMarkdown($pageId, InsertContent::create("Appended"));
$pages->updateMarkdown($pageId, InsertContent::atStart("Prepended"));
$pages->updateMarkdown($pageId, InsertContent::atEnd("Appended"));
$pages->updateMarkdown($pageId, InsertContent::after("Intro...paragraph", "Inserted"));

// Replace an ellipsis-based range
$pages->updateMarkdown($pageId, ReplaceContentRange::create("Old...section", "New section"));
```

Updates that would delete child pages or databases are rejected by default.
Call `allowDeletingContent()` on `UpdateContent`, `ReplaceContent` or
`ReplaceContentRange` to permit it.
