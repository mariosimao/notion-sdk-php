# Tab

Tab blocks are tabbed containers. Each tab is a [Paragraph](./Paragraph) child:

- the paragraph text is the tab label;
- the paragraph icon (optional) is the tab icon;
- the paragraph children are the tab content.

Only paragraphs can be direct children of a tab block. Adding any other block
type throws a `Notion\Exceptions\BlockException\TabException`.

## Create

```php
$overview = Paragraph::fromString("Overview")
    ->changeIcon(Emoji::fromString("📋"))
    ->addChild(Paragraph::fromString("Tab 1 content"));

$details = Paragraph::fromString("Details")
    ->changeIcon(Emoji::fromString("🔍"))
    ->addChild(Paragraph::fromString("Tab 2 content"));

$block = Tab::create($overview, $details);

$notion->blocks()->append($pageId, [ $block ]);
```

## Add tab

```php
$block = Tab::create($overview)->addTab($details);
```

## Change tabs

```php
$block = $block->changeTabs($overview, $details);

$block->tabs; // Paragraph[]
```

## Read tabs

Like other blocks with children, the tabs of an existing tab block are not
included in the API response. Use `findChildrenRecursive()` to load them:

```php
$block = $notion->blocks()->find($tabBlockId);
$tabs = $notion->blocks()->findChildrenRecursive($tabBlockId);
$block = $block->changeChildren(...$tabs);

foreach ($block->tabs as $tab) {
    $tab->toString(); // Tab label
    $tab->icon;       // Icon|null
    $tab->children;   // Tab content
}
```
